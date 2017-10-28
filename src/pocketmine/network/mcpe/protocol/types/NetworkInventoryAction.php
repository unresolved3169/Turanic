<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\inventory\transaction\action\CreativeInventoryAction;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\CraftingTransferMaterialAction;
use pocketmine\inventory\transaction\action\CraftingTakeResultAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\action\UnknownAction;
use pocketmine\item\Item;
use pocketmine\inventory\EnchantInventory;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Player;

class NetworkInventoryAction{
	
	const SOURCE_CONTAINER = 0;
	const SOURCE_GLOABAL = 1;
	const SOURCE_WORLD = 2;
	const SOURCE_CREATIVE = 3;
	const SOURCE_TODO = 99999;

	/**
	 * Fake window IDs for the SOURCE_TODO type (99999)
	 *
	 * These identifiers are used for inventory source types which are not currently implemented server-side in MCPE.
	 * As a general rule of thumb, anything that doesn't have a permanent inventory is client-side. These types are
	 * to allow servers to track what is going on in client-side windows.
	 *
	 * Expect these to change in the future.
	 */
	const SOURCE_TYPE_CRAFTING_ADD_INGREDIENT = 1;
	const SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT = 2;
	const SOURCE_TYPE_CRAFTING_RESULT = 3;
	const SOURCE_TYPE_CRAFTING_USE_INGREDIENT = 4;

	const SOURCE_TYPE_ANVIL_INPUT = 9;
	const SOURCE_TYPE_ANVIL_MATERIAL = 10;
	const SOURCE_TYPE_ANVIL_RESULT = 11;
	const SOURCE_TYPE_ANVIL_OUTPUT = 12;

	const SOURCE_TYPE_ENCHANT_INPUT = 14;
	const SOURCE_TYPE_ENCHANT_MATERIAL = 15;
	const SOURCE_TYPE_ENCHANT_OUTPUT = 16;

	const SOURCE_TYPE_TRADING_INPUT_1 = 21;
	const SOURCE_TYPE_TRADING_INPUT_2 = 22;
	const SOURCE_TYPE_TRADING_USE_INPUTS = 23;
	const SOURCE_TYPE_TRADING_OUTPUT = 24;

	const SOURCE_TYPE_BEACON = 23;

	/** Any client-side window dropping its contents when the player closes it */
	const SOURCE_TYPE_CONTAINER_DROP_CONTENTS = 100;

	const ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM = 0;
	const ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM = 1;

	const ACTION_MAGIC_SLOT_DROP_ITEM = 0;
	const ACTION_MAGIC_SLOT_PICKUP_ITEM = 1;
	
	const FLAG_RANDOM = 0;

	/** @var int */
	public $sourceType;
	/** @var int */
	public $windowId = ContainerIds::NONE;
	/** @var int */
	public $flags = self::FLAG_RANDOM;
	/** @var int */
	public $inventorySlot;
	/** @var Item */
	public $oldItem;
	/** @var Item */
	public $newItem;

	/**
	 * @param InventoryTransactionPacket $packet
	 * @return $this
	 */
	public function read(InventoryTransactionPacket $packet){
		$this->sourceType = $packet->getUnsignedVarInt();

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
			case self::SOURCE_TODO:
				$this->windowId = $packet->getVarInt();
				break;
			case self::SOURCE_WORLD:
				$this->flags = $packet->getUnsignedVarInt();
				break;
			case self::SOURCE_CREATIVE:
				break;
		}

		$this->inventorySlot = $packet->getUnsignedVarInt();
		$this->oldItem = $packet->getSlot();
		$this->newItem = $packet->getSlot();

		return $this;
	}

	/**
	 * @param InventoryTransactionPacket $packet
	 */
	public function write(InventoryTransactionPacket $packet){
		$packet->putUnsignedVarInt($this->sourceType);

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
			case self::SOURCE_TODO:
				$packet->putVarInt($this->windowId);
				break;
			case self::SOURCE_WORLD:
				$packet->putUnsignedVarInt($this->flags);
				break;
			case self::SOURCE_CREATIVE:
				break;
		}

		$packet->putUnsignedVarInt($this->inventorySlot);
		$packet->putSlot($this->oldItem);
		$packet->putSlot($this->newItem);
	}

	/**
	 * @param Player $player
	 *
	 * @return InventoryAction|null
	 */
	public function createInventoryAction(Player $player){
		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				if($this->windowId === ContainerIds::ARMOR){
					$this->inventorySlot += 36;
					$this->windowId = ContainerIds::INVENTORY;
				}

				$window = $player->getWindow($this->windowId);
				if($window !== null){
					return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
				}

				return null;
			case self::SOURCE_WORLD:
				return new DropItemAction($this->oldItem, $this->newItem);
			case self::SOURCE_CREATIVE:
				switch($this->inventorySlot){
					case self::ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM:
						$type = CreativeInventoryAction::TYPE_DELETE_ITEM;
						break;
					case self::ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM:
						$type = CreativeInventoryAction::TYPE_CREATE_ITEM;
						break;
					default:
						return null;
				}

				return new CreativeInventoryAction($this->oldItem, $this->newItem, $type);
			case self::SOURCE_TODO:
				//These types need special handling.
				switch($this->windowId){
					case self::SOURCE_TYPE_CRAFTING_ADD_INGREDIENT:
					case self::SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT:
						$window = $player->getCraftingGrid();
						return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_CRAFTING_RESULT:
						return new CraftingTakeResultAction($this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
						return new CraftingTransferMaterialAction($this->oldItem, $this->newItem, $this->inventorySlot);
					case self::SOURCE_TYPE_CONTAINER_DROP_CONTENTS:
						$window = $player->getCraftingGrid();

						$inventorySlot = $window->first($this->oldItem, true);
						if($inventorySlot === -1){
							return null;
						}
						return new SlotChangeAction($window, $inventorySlot, $this->oldItem, $this->newItem);
				}

				return new UnknownAction();
		}

		return null;
 }
}
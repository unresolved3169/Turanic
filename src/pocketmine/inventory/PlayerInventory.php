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

namespace pocketmine\inventory;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\Player;
use pocketmine\Server;

class PlayerInventory extends EntityInventory{

    const CURSOR_INDEX = -1;

	/** @var Human */
	protected $holder;

	/** @var int */
	protected $itemInHandIndex = 0;

    /** @var Item */
    protected $cursor = null;

	/**
	 * @param Human $player
	 */
	public function __construct(Human $player){
		parent::__construct($player);
	}

    public function sendCursor(){
        $pk = new InventorySlotPacket();
        $pk->inventorySlot = 0;
        $pk->item = clone $this->cursor;
        $pk->windowId = ContainerIds::CURSOR;
        $this->getHolder()->dataPacket($pk);
    }

	public function getName() : string{
		return "Player";
	}

	public function getDefaultSize() : int{
		return 40; //36 inventory, 4 armor
	}

	public function getSize() : int{
		return parent::getSize() - 4; //Remove armor slots
	}

	public function setSize(int $size){
		parent::setSize($size + 4);
		$this->sendContents($this->getViewers());
	}

    /**
     * Called when a client equips a hotbar slot. This method should not be used by plugins.
     * This method will call PlayerItemHeldEvent.
     *
     * @param int $hotbarSlot Number of the hotbar slot to equip.
     * @param int|null $inventorySlot Inventory slot to map to the specified hotbar slot. Supply null to make no change to the link.
     *
     * @return bool if the equipment change was successful, false if not.
     */
    public function equipItem(int $hotbarSlot, int $inventorySlot = null) : bool{
        if($inventorySlot === null){
            $inventorySlot = $this->isHotbarSlot($hotbarSlot) ? $hotbarSlot : -1;
        }
        if($hotbarSlot < 0 or $hotbarSlot >= $this->getHotbarSize() or $inventorySlot < -1 or $inventorySlot >= $this->getSize()){
            $this->sendContents($this->getHolder());
            return false;
        }
        if($inventorySlot === -1){
            $item = Item::get(Item::AIR, 0, 0);
        }else{
            $item = $this->getItem($inventorySlot);
        }
        $this->getHolder()->getLevel()->getServer()->getPluginManager()->callEvent($ev = new PlayerItemHeldEvent($this->getHolder(), $item, $hotbarSlot));
        if($ev->isCancelled()){
            $this->sendContents($this->getHolder());
            return false;
        }
        if (!($hotbarSlot == $inventorySlot || $inventorySlot < 0)) {
            $tmp = $this->getItem($hotbarSlot);
            $this->setItem($hotbarSlot, $this->getItem($inventorySlot));
            $this->setItem($inventorySlot, $tmp);
        }
        $this->setHeldItemIndex($hotbarSlot, false);
        return true;
    }

	private function isHotbarSlot(int $slot) : bool{
		return $slot >= 0 and $slot <= $this->getHotbarSize();
	}

	/**
	 * @param int $slot
	 * @throws \InvalidArgumentException
	 */
	private function throwIfNotHotbarSlot(int $slot){
		if(!$this->isHotbarSlot($slot)){
			throw new \InvalidArgumentException("$slot is not a valid hotbar slot index (expected 0 - " . ($this->getHotbarSize() - 1) . ")");
		}
	}

	/**
	 * Returns the item in the specified hotbar slot.
	 *
	 * @param int $hotbarSlot
	 * @return Item
	 *
	 * @throws \InvalidArgumentException if the hotbar slot index is out of range
	 */
	public function getHotbarSlotItem(int $hotbarSlot) : Item{
		$this->throwIfNotHotbarSlot($hotbarSlot);
		return $this->getItem($hotbarSlot);
	}

	/**
	 * @deprecated
	 * @return int
	 */
	public function getHeldItemSlot() : int{
		return $this->getHeldItemIndex();
	}

	/**
	 * Returns the hotbar slot number the holder is currently holding.
	 * @return int
	 */
	public function getHeldItemIndex() : int{
		return $this->itemInHandIndex;
	}

	/**
	 * Sets which hotbar slot the player is currently loading.
	 *
	 * @param int  $hotbarSlot 0-8 index of the hotbar slot to hold
	 * @param bool $send Whether to send updates back to the inventory holder. This should usually be true for plugin calls.
	 *                    It should only be false to prevent feedback loops of equipment packets between client and server.
	 *
	 * @throws \InvalidArgumentException if the hotbar slot is out of range
	 */
	public function setHeldItemIndex(int $hotbarSlot, bool $send = true){
		$this->throwIfNotHotbarSlot($hotbarSlot);

		$this->itemInHandIndex = $hotbarSlot;

		if($this->getHolder() instanceof Player and $send){
			$this->sendHeldItem($this->getHolder());
		}

		$this->sendHeldItem($this->getHolder()->getViewers());
	}

	/**
	 * Returns the currently-held item.
	 *
	 * @return Item
	 */
	public function getItemInHand() : Item{
		return $this->getHotbarSlotItem($this->itemInHandIndex);
	}

	/**
	 * Sets the item in the currently-held slot to the specified item.
	 *
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function setItemInHand(Item $item) : bool{
		return $this->setItem($this->getHeldItemIndex(), $item);
	}

	public function setItem(int $index, Item $item, bool $send = true) : bool{
        if($index >= 0){
            if($item->isNull()){
                $item = Item::get(Item::AIR, 0, 0);
            }else{
                $item = clone $item;
            }
            if($index >= $this->getSize()){ //Armor change
                Server::getInstance()->getPluginManager()->callEvent($ev = new EntityArmorChangeEvent($this->getHolder(), $this->getItem($index), $item, $index));
                if($ev->isCancelled() and $this->getHolder() instanceof Human){
                    $this->sendArmorSlot($index, $this->getViewers());
                    return false;
                }
                $item = $ev->getNewItem();
            }else{
                Server::getInstance()->getPluginManager()->callEvent($ev = new EntityInventoryChangeEvent($this->getHolder(), $this->getItem($index), $item, $index));
                if($ev->isCancelled()){
                    $this->sendSlot($index, $this->getViewers());
                    return false;
                }
                $item = $ev->getNewItem();
            }
            $old = $this->getItem($index);
            $this->slots[$index] = $item;
            $this->onSlotChange($index, $old, $send);
            return true;
        }elseif($index === self::CURSOR_INDEX){
            $this->cursor = $item;
            if($send)
                $this->sendCursor();
            return true;
        }
        return parent::setItem($index, $item, $send);
    }

    public function getItem(int $index): Item{
        if($index === self::CURSOR_INDEX){
            return $this->cursor === null ? Item::get(Item::AIR) : clone $this->cursor;
        }else{
            return parent::getItem($index);
        }
	}

    /**
	 * Sends the currently-held item to specified targets.
	 * @param Player|Player[] $target
	 */
	public function sendHeldItem($target){
		$item = $this->getItemInHand();

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->item = $item;
		$pk->inventorySlot = $pk->hotbarSlot = $this->getHeldItemIndex();
		$pk->windowId = ContainerIds::INVENTORY;

		if(!is_array($target)){
			$target->dataPacket($pk);
			if($target === $this->getHolder()){
				$this->sendSlot($this->getHeldItemIndex(), $target);
			}
		}else{
			$this->getHolder()->getLevel()->getServer()->broadcastPacket($target, $pk);
			if(in_array($this->getHolder(), $target, true)){
				$this->sendSlot($this->getHeldItemIndex(), $this->getHolder());
			}
		}
	}

	public function onSlotChange(int $index, Item $before, bool $send) {
		$holder = $this->getHolder();
		if($holder instanceof Player and !$holder->spawned){
			return;
		}

		if($index >= $this->getSize()){
			$this->sendArmorSlot($index, $this->getViewers());
			$this->sendArmorSlot($index, $this->getHolder()->getViewers());
		}else{
			//Do not send armor by accident here.
			parent::onSlotChange($index, $before, $send);
		}
	}

	/**
	 * Returns the number of slots in the hotbar.
	 * @return int
	 */
	public function getHotbarSize() : int{
		return 9;
	}

	public function getArmorItem(int $index) : Item{
		return $this->getItem($this->getSize() + $index);
	}
	
	/**
	 * @param $index
	 * @param $cost
	 */
	public function damageArmor(int $index, int $cost){
		$this->slots[$this->getSize() + $index]->useOn($this->slots[$this->getSize() + $index], $cost);
		if($this->slots[$this->getSize() + $index]->getDamage() >= $this->slots[$this->getSize() + $index]->getMaxDurability()){
			$this->setItem($this->getSize() + $index, Item::get(Item::AIR, 0, 0));
		}
		$this->sendArmorContents($this->getViewers());
	}

	public function setArmorItem(int $index, Item $item) : bool{
		return $this->setItem($this->getSize() + $index, $item);
	}

	public function getHelmet() : Item{
		return $this->getItem($this->getSize());
	}

	public function getChestplate() : Item{
		return $this->getItem($this->getSize() + 1);
	}

	public function getLeggings() : Item{
		return $this->getItem($this->getSize() + 2);
	}

	public function getBoots() : Item{
		return $this->getItem($this->getSize() + 3);
	}

	public function setHelmet(Item $helmet) : bool{
		return $this->setItem($this->getSize(), $helmet);
	}

	public function setChestplate(Item $chestplate) : bool{
		return $this->setItem($this->getSize() + 1, $chestplate);
	}

	public function setLeggings(Item $leggings) : bool{
		return $this->setItem($this->getSize() + 2, $leggings);
	}

	public function setBoots(Item $boots) : bool{
		return $this->setItem($this->getSize() + 3, $boots);
	}

	protected function doSetItemEvents(int $index, Item $newItem){
		if($index >= $this->getSize()){
			Server::getInstance()->getPluginManager()->callEvent($ev = new EntityArmorChangeEvent($this->getHolder(), $this->getItem($index), $newItem, $index));
			if($ev->isCancelled()){
				return null;
			}

			return $ev->getNewItem();
		}

		return parent::doSetItemEvents($index, $newItem);
	}

	public function clearAll() {
		parent::clearAll();

		for($i = $this->getSize(), $m = $i + 4; $i < $m; ++$i){
			$this->clear($i, false);
		}

		$this->sendArmorContents($this->getViewers());
	}

	/**
	 * @return Item[]
	 */
	public function getArmorContents() : array{
		$armor = [];

		for($i = 0; $i < 4; ++$i){
			$armor[$i] = $this->getItem($this->getSize() + $i);
		}

		return $armor;
	}

	/**
	 * @param Player|Player[] $target
	 */
	public function sendArmorContents($target){
		if($target instanceof Player){
			$target = [$target];
		}

		$armor = $this->getArmorContents();

		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->slots = $armor;
		$pk->encode();

		foreach($target as $player){
			if($player === $this->getHolder()){
				$pk2 = new InventoryContentPacket();
				$pk2->windowId = ContainerIds::ARMOR;
				$pk2->items = $armor;
				$player->dataPacket($pk2);
			}else{
				$player->dataPacket($pk);
			}
		}
	}

	/**
	 * @param Item[] $items
	 */
	public function setArmorContents(array $items){
		for($i = 0; $i < 4; ++$i){
			if(!isset($items[$i]) or !($items[$i] instanceof Item)){
				$items[$i] = Item::get(Item::AIR, 0, 0);
			}

			$this->setItem($this->getSize() + $i, $items[$i], false);
		}

		$this->sendArmorContents($this->getViewers());
	}


	/**
	 * @param int             $index
	 * @param Player|Player[] $target
	 */
	public function sendArmorSlot(int $index, $target){
		if($target instanceof Player){
			$target = [$target];
		}

		$armor = $this->getArmorContents();

		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->slots = $armor;
		$pk->encode();

		foreach($target as $player){
			if($player === $this->getHolder()){
				/** @var Player $player */

				$pk2 = new InventorySlotPacket();
				$pk2->windowId = ContainerIds::ARMOR;
				$pk2->inventorySlot = $index - $this->getSize();
				$pk2->item = $this->getItem($index);
				$player->dataPacket($pk2);
			}else{
				$player->dataPacket($pk);
			}
		}
	}

	public function sendCreativeContents(){
		$pk = new InventoryContentPacket();
		$pk->windowId = ContainerIds::CREATIVE;

		if(!$this->getHolder()->isSpectator()){ //fill it for all gamemodes except spectator
			foreach(Item::getCreativeItems() as $i => $item){
				$pk->items[$i] = clone $item;
			}
		}

		$this->getHolder()->dataPacket($pk);
	}

	/**
	 * This override is here for documentation and code completion purposes only.
	 * @return Human|Player
	 */
	public function getHolder(){
		return $this->holder;
	}

}
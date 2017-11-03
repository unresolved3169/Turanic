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

namespace pocketmine\inventory\transaction;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

/**
 * This InventoryTransaction only allows doing Transaction between one / two inventories
 */
class InventoryTransaction{
	/** @var float */
	private $creationTime;
	protected $hasExecuted = false;
	/** @var Player */
	protected $source;

	/** @var Inventory[] */
	protected $inventories = [];

	/** @var InventoryAction[] */
	protected $actions = [];

	/**
	 * @param Player            $source
	 */
	public function __construct(Player $source){
		$this->creationTime = microtime(true);
		$this->source = $source;
	}

	/**
	 * @return Player
	 */
	public function getSource() : Player{
		return $this->source;
	}

	public function getCreationTime() : float{
		return $this->creationTime;
	}

	/**
	 * @return Inventory[]
	 */
	public function getInventories() : array{
		return $this->inventories;
	}

	/**
	 * @return InventoryAction[]
	 */
	public function getActions() : array{
		return $this->actions;
	}

	/**
	 * @param InventoryAction $action
	 */
	public function addAction(InventoryAction $action) {
		if(!isset($this->actions[$hash = spl_object_hash($action)]))
		    return;

		if($action instanceof SlotChangeAction){
			$action->setInventoryFrom($this->source);
            $this->inventories[spl_object_hash($action->getInventory())] = $action->getInventory();
		}
        $this->actions[spl_object_hash($action)] = $action;
	}

	/**
	 * @internal This method should not be used by plugins, it's used to add tracked inventories for InventoryActions
	 * involving inventories.
	 *
	 * @param Inventory $inventory
	 */
	public function addInventory(Inventory $inventory) {
		if(!isset($this->inventories[$hash = spl_object_hash($inventory)])){
			$this->inventories[$hash] = $inventory;
		}
	}

	/**
	 * @param Item[] $needItems
	 * @param Item[] $haveItems
	 *
	 * @return bool
	 */
	protected function matchItems(array &$needItems, array &$haveItems) : bool{
        foreach($this->actions as $key => $action){
            if($action->getTargetItem()->getId() !== Item::AIR){
                $needItems[] = $action->getTargetItem();
            }

            if($action->getSourceItem()->getId() !== Item::AIR){
                $haveItems[] = $action->getSourceItem();
            }
        }

        foreach($needItems as $i => $needItem){
            foreach($haveItems as $j => $haveItem){
                if($needItem->equals($haveItem)){
                    $amount = min($needItem->getCount(), $haveItem->getCount());
                    $needItem->setCount($needItem->getCount() - $amount);
                    $haveItem->setCount($haveItem->getCount() - $amount);
                    if($haveItem->getCount() === 0){
                        unset($haveItems[$j]);
                    }
                    if($needItem->getCount() === 0){
                        unset($needItems[$i]);
                        break;
                    }
                }
            }
        }

        return true;
	}

	/**
	 * @return bool
	 */
	public function canExecute() : bool{
		$haveItems = [];
		$needItems = [];
		return $this->matchItems($needItems, $haveItems) and count($this->actions) > 0 and count($haveItems) === 0 and count($needItems) === 0;
	}

	protected function sendInventories() {
		foreach($this->inventories as $inventory){
			$inventory->sendContents($this->source);
			if($inventory instanceof PlayerInventory){
				$inventory->sendArmorContents($this->source);
			}
		}
	}

	/**
	 * Executes the group of actions, returning whether the transaction executed successfully or not.
	 * @return bool
	 */
	public function execute() : bool{
		if($this->hasExecuted() or !$this->canExecute()){
			return false;
		}

        Server::getInstance()->getPluginManager()->callEvent($ev = new InventoryTransactionEvent($this));
		if($ev->isCancelled()){
			return false;
		}

        $actions = $this->actions;
		foreach($actions as $action){
            if(!$action->isValid($this->source) or $action->isAlreadyDone($this->source)){
                unset($actions[spl_object_hash($action)]);
            }
		}

		foreach($this->actions as $action){
			if($action->execute($this->source)){
				$action->onExecuteSuccess($this->source);
			}else{
				$action->onExecuteFail($this->source);
			}
		}

		$this->hasExecuted = true;

		return true;
	}

	/**
	 * @return bool
	 */
	public function hasExecuted() : bool{
		return $this->hasExecuted;
	}
}
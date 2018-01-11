<?php

/*
 *
 *    _______                    _
 *   |__   __|                  (_)
 *      | |_   _ _ __ __ _ _ __  _  ___
 *      | | | | | '__/ _` | '_ \| |/ __|
 *      | | |_| | | | (_| | | | | | (__
 *      |_|\__,_|_|  \__,_|_| |_|_|\___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Turanic
 *
 */

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\event\inventory\InventoryClickEvent;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;

/**
 * Represents an action causing a change in an inventory slot.
 */
class SlotChangeAction extends InventoryAction{

    /** @var Inventory */
    protected $inventory;
    /** @var int */
    private $inventorySlot;

    /**
     * @param Inventory $inventory
     * @param int       $inventorySlot
     * @param Item      $sourceItem
     * @param Item      $targetItem
     */
    public function __construct(Inventory $inventory, int $inventorySlot, Item $sourceItem, Item $targetItem){
        parent::__construct($sourceItem, $targetItem);
        $this->inventory = $inventory;
        $this->inventorySlot = $inventorySlot;
    }

    /**
     * Returns the inventory involved in this action.
     *
     * @return Inventory
     */
    public function getInventory() : Inventory{
        return $this->inventory;
    }

    /**
     * Returns the slot in the inventory which this action modified.
     * @return int
     */
    public function getSlot() : int{
        return $this->inventorySlot;
    }

    /**
     * Checks if the item in the inventory at the specified slot is the same as this action's source item.
     *
     * @param Player $source
     *
     * @return bool
     */
    public function isValid(Player $source) : bool{
        return (
            $this->inventory->slotExists($this->inventorySlot) and
            $this->inventory->getItem($this->inventorySlot)->equalsExact($this->sourceItem)
        );
    }

    public function onPreExecute(Player $source) : bool{
        $source->getServer()->getPluginManager()->callEvent($ev = new InventoryClickEvent($this->inventory, $source, $this->inventorySlot));
        if($ev->isCancelled()){
            return false;
        }

        return true;
    }

    /**
     * Adds this action's target inventory to the transaction's inventory list.
     *
     * @param InventoryTransaction $transaction
     *
     */
    public function onAddToTransaction(InventoryTransaction $transaction) {
        $transaction->addInventory($this->inventory);
    }

    /**
     * Sets the item into the target inventory.
     *
     * @param Player $source
     *
     * @return bool
     */
    public function execute(Player $source) : bool{
        return $this->inventory->setItem($this->inventorySlot, $this->targetItem, false);
    }

    /**
     * Sends slot changes to other viewers of the inventory. This will not send any change back to the source Player.
     *
     * @param Player $source
     */
    public function onExecuteSuccess(Player $source) {
        $viewers = $this->inventory->getViewers();
        unset($viewers[spl_object_hash($source)]);
        $this->inventory->sendSlot($this->inventorySlot, $viewers);
    }

    /**
     * Sends the original slot contents to the source player to revert the action.
     *
     * @param Player $source
     */
    public function onExecuteFail(Player $source) {
        $this->inventory->sendSlot($this->inventorySlot, $source);
    }
}
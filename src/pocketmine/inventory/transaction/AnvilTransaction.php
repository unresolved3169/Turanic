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

namespace pocketmine\inventory\transaction;

use pocketmine\inventory\transaction\action\AnvilMaterialAction;
use pocketmine\inventory\transaction\action\AnvilResultAction;
use pocketmine\inventory\transaction\action\AnvilInputAction;
use pocketmine\inventory\Inventory;
use pocketmine\item\EnchantedBook;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;

class AnvilTransaction extends InventoryTransaction {

    /** @var float */
    private $creationTime;

    /** @var Item */
    private $result;
    private $useInput;
    private $useMaterial;

    /** @var Inventory */
    private $inventory;

    public function __construct(Player $source, $actions = []){
        $this->creationTime = microtime(true);
        $this->source = $source;

        foreach($actions as $action){
            if($action instanceof AnvilResultAction){
                if($this->result == null){
                    $this->result = $action->getSourceItem();
                    $this->inventory = $action->getInventory();
                }
            }elseif($action instanceof AnvilInputAction){
                if($this->useInput == null){
                    $this->useInput = $action->getSourceItem();
                }
                continue;
            }elseif($action instanceof AnvilMaterialAction){
                if($this->useMaterial == null){
                    $this->useMaterial = $action->getTargetItem();
                }
                continue;
            }
            $this->addAction($action);
        }
    }

    public function execute(): bool{
        if($this->hasExecuted() or !$this->canExecute()){
            $this->sendInventories();
            return false;
        }

        if(!$this->callExecuteEvent()){
            $this->sendInventories();
            return false;
        }

        foreach($this->actions as $action){
            if(!$action->onPreExecute($this->source)){
                $this->sendInventories();
                return false;
            }
        }

        foreach($this->actions as $action){
            if($action->execute($this->source)){
                $action->onExecuteSuccess($this->source);
            }else{
                $action->onExecuteFail($this->source);
            }
        }

        $this->inventory->setItem(0, Item::get(0), false);
        if ($this->useMaterial != null) {
            $item = $this->inventory->getItem(1);
            if ($item->getCount() - $this->useMaterial->getCount() < 1) {
                $item = Item::get(0);
            } else {
                $item = $item->setCount($item->getCount() - $this->useMaterial->getCount());
            }
            $this->inventory->setItem(1, $item, false);
        }

        $cost = $this->useInput->getRepairCost();
        if ($this->useInput->getCustomName() !== $this->result->getCustomName()) {
            $cost++;
        }
        if ($this->useMaterial != null) {
            $cost += $this->useMaterial->getRepairCost();
            if ($this->useMaterial instanceof EnchantedBook) {
                foreach ($this->result->getEnchantments() as $enchant) {
                    $inputEnchant = $this->useInput->getEnchantment($enchant->getId());
                    if ($inputEnchant == null) {
                        $cost += $enchant->getRepairCost(true);
                    } else if ($enchant->getLevel() != $inputEnchant->getLevel()) {
                        $check = Enchantment::getEnchantment($enchant->getId());
                        $check = new EnchantmentInstance($check, $enchant->getLevel() - $inputEnchant->getLevel());
                        $cost += $check->getRepairCost(true);
                    }
                }
            } else if ($this->useMaterial->isTool()) {
                $ench = 0;
                foreach ($this->result->getEnchantments() as $enchant) {
                    $ench += $enchant->getRepairCost(false);
                }
                foreach ($this->useInput->getEnchantments() as $enchant) {
                    $ench -= $enchant->getRepairCost(false);
                }
                $cost += $ench;
            } else {
                $cost += $this->useMaterial->getCount();
            }
        }
        $this->source->setXpLevel($this->source->getXpLevel() - $cost);

        $this->hasExecuted = true;

        return true;
    }

}
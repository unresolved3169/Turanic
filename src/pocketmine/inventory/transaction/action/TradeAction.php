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

use pocketmine\inventory\VillagerTradeInventory;
use pocketmine\item\Item;
use pocketmine\Player;

class TradeAction extends InventoryAction{

    /** @var VillagerTradeInventory|null */
    protected $inventory;

    public function __construct(Item $sourceItem, Item $targetItem, VillagerTradeInventory $inventory = null){
        parent::__construct($sourceItem, $targetItem);
        $this->inventory = $inventory; //TODO
    }

    public function isValid(Player $source): bool{
        return true;
    }

    public function execute(Player $source): bool{
        return true;
    }

    public function onExecuteSuccess(Player $source){
    }

    public function onExecuteFail(Player $source){
    }
}
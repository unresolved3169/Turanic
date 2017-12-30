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

use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\Player;

/**
 * Represents an action involving dropping an item into the world.
 */
class DropItemAction extends InventoryAction{

    /**
     * Verifies that the source item of a drop-item action must be air. This is not strictly necessary, just a sanity
     * check.
     *
     * @param Player $source
     * @return bool
     */
    public function isValid(Player $source) : bool{
        return $this->sourceItem->isNull();
    }

    public function onPreExecute(Player $source) : bool{
        $source->getServer()->getPluginManager()->callEvent($ev = new PlayerDropItemEvent($source, $this->targetItem));
        if($ev->isCancelled()){
            return false;
        }

        return true;
    }

    /**
     * Drops the target item in front of the player.
     *
     * @param Player $source
     * @return bool
     */
    public function execute(Player $source) : bool{
        return $source->dropItem($this->targetItem);
    }

    public function onExecuteSuccess(Player $source){

    }

    public function onExecuteFail(Player $source){

    }
}
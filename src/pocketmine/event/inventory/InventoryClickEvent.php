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

namespace pocketmine\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\inventory\Inventory;
use pocketmine\Player;

class InventoryClickEvent extends InventoryEvent implements Cancellable {

    public static $handlerList = null;

    /** @var Player */
    private $player;

    /**
     * @param Inventory $inventory
     * @param Player $player
     */
    public function __construct(Inventory $inventory, Player $player){
        $this->player = $player;
        parent::__construct($inventory);
    }

    /**
     * @return Player
     */
    public function getPlayer(){
        return $this->player;
    }
}
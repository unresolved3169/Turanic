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

namespace pocketmine\inventory;

use pocketmine\Player;

class CraftingGrid extends BaseInventory{

    protected $result = null;

    public $type = Player::CRAFTING_SMALL;

    public function __construct(Player $holder){
        parent::__construct($holder);
    }

    public function getGridWidth() : int{
        return 2;
    }

    public function getDefaultSize() : int{
        return $this->getGridWidth() ** 2;
    }

    public function setSize(int $size){
        throw new \BadMethodCallException("Cannot change the size of a crafting grid");
    }

    public function getName() : string{
        return "Crafting";
    }

    public function sendSlot(int $index, $target){
        //we can't send a slot of a client-sided inventory window
    }

    public function sendContents($target){
        //no way to do this
    }
}
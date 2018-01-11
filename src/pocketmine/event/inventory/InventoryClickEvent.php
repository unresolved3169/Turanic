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

namespace pocketmine\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;

class InventoryClickEvent extends InventoryEvent implements Cancellable {

    public static $handlerList = null;

    /** @var Player */
    private $player;
    /** @var int */
    private $slot;
    /** @var Item */
    private $item;

    public function __construct(Inventory $inventory, Player $player, int $slot){
        $this->player = $player;
        $this->slot = $slot;
        parent::__construct($inventory);
        $this->item = $this->inventory->getItem($slot);
    }

    public function getPlayer() : Player{
        return $this->player;
    }

    public function getSlot() : int{
        return $this->slot;
    }

    public function getItem() : Item{
        return $this->item;
    }

}
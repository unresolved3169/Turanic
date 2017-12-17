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

use pocketmine\item\Item;
use pocketmine\Player;

class CraftingGrid extends BaseInventory{

    const WINDOW_ID = -1;

    const RESULT_INDEX = -1;

    protected $result = null;

    public $type = Player::CRAFTING_SMALL;

    public function __construct(Player $holder){
        parent::__construct($holder);
    }

    public function getDefaultSize() : int{
        return $this->getGridWidth() ** 2;
    }

    public function getItem(int $slot) : Item{
        if($slot === self::RESULT_INDEX){
            return $this->result === null ? Item::get(Item::AIR) : clone $this->result;
        }else{
            return parent::getItem($slot);
        }
    }

    public function setItem(int $slot, Item $item, bool $send = true) : bool{
        if($slot === self::RESULT_INDEX){
            $this->result = clone $item;
            return true;
        }else{
            return parent::setItem($slot, $item, $send);
        }
    }

    public function setSize(int $size){
        throw new \BadMethodCallException("Cannot change the size of a crafting grid");
    }

    public function getName() : string{
        return "Crafting";
    }

    public function getGridWidth() : int{
        return 2;
    }

    public function sendSlot(int $index, $target){
    }

    public function sendContents($target){
        //no way to do this
    }
}
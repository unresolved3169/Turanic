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

namespace pocketmine\inventory;

use pocketmine\Player;
use pocketmine\tile\VirtualHolder;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class VirtualInventory extends CustomInventory {

    /** @var  VirtualHolder */
    protected $holder;

    public function __construct(VirtualHolder $tile){
        parent::__construct($tile);
    }

    public function getHolder(){
        return $this->holder;
    }

    public function onClose(Player $who){
        $this->holder->cevir($who);
        parent::onClose($who);
        $this->holder->close();
    }
    
    public function getNetworkType() : int{
    	return WindowTypes::CONTAINER;
    }

    public function getName(): string{
        return "VirtualHolder";
    }

    public function getDefaultSize(): int{
        return 27;
    }
}
<?php

/*
 *
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
 *
*/

namespace pocketmine\inventory;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\ShulkerBox;

class ShulkerBoxInventory extends ContainerInventory {

    protected $holder;

    public function __construct(ShulkerBox $tile){
        parent::__construct($tile);
    }

    public function getName(): string{
        return "Shulker Box";
    }

    public function getDefaultSize(): int{
        return 27;
    }

    /**
     * Returns the Minecraft PE inventory type used to show the inventory window to clients.
     * @return int
     */
    public function getNetworkType(): int{
        return WindowTypes::CONTAINER;
    }

    public function onClose(Player $who){
        if(count($this->getViewers()) === 1){
            $this->broadcastBlockEventPacket($this->getHolder(), false);
        }

        parent::onClose($who);
    }

    public function onOpen(Player $who){
        parent::onOpen($who);

        if(count($this->getViewers()) === 1){
            $this->broadcastBlockEventPacket($this->getHolder(), true);
        }
    }

    protected function broadcastBlockEventPacket(Vector3 $vector, bool $isOpen) {
        $pk = new BlockEventPacket();
        $pk->x = (int) $vector->x;
        $pk->y = (int) $vector->y;
        $pk->z = (int) $vector->z;
        $pk->eventType  = 1;
        $pk->eventData = +$isOpen;
        $this->getHolder()->getLevel()->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
    }

    /**
     * @return ShulkerBox
     */
    public function getHolder(){
        return $this->holder;
    }
}
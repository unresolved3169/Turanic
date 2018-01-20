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

use pocketmine\entity\passive\Villager;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\Player;
use pocketmine\utils\MainLogger;
use Symfony\Component\Filesystem\Exception\IOException;

class VillagerTradeInventory extends BaseInventory {

    /** @var Villager */
    protected $holder;

    public function __construct(Villager $holder){
        parent::__construct($holder);
    }

    public function getName(): string{
        return $this->holder->getName();
    }

    public function getDefaultSize(): int{
        return 2;
    }

    public function getHolder() : Villager{
        return $this->holder;
    }

    public function onOpen(Player $who){
        $offers = $this->holder->getOffers();
        if(empty($offers->getListTag("Recipes"))){
            parent::close($who);
            return;
        }

        parent::onOpen($who);

        $pk = new UpdateTradePacket();
        $pk->windowId = $who->getWindowId($this);
        $pk->varint1 = 0;
        $pk->varint2 = 0;
        $pk->isWilling = true;
        $pk->traderEid = $this->holder->getId();
        $pk->playerEid = $who->getId();
        $pk->displayName = $this->getName();

        try{
            $nbt = new NetworkLittleEndianNBTStream();
            $nbt->setData($offers);
            $pk->offers = $nbt->write();
        }catch(IOException $exception){
            MainLogger::getLogger()->logException($exception);
        }

        $who->dataPacket($pk);
    }

    public function onClose(Player $who){
        foreach ($this->getContents() as $slot => $content) {
            if($who->getInventory()->canAddItem($content)){
                $who->getInventory()->addItem($content);
            }else{
                $who->dropItem($content);
            }
            $this->clear($slot);
        }

        parent::close($who);
    }
}
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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\block\utils\RedstoneUtils;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

abstract class Button extends Flowable {

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getHardness(): float{
        return 0.5;
    }

    public function getVariantBitmask(): int{
        return 0;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $this->meta = $face;

        return $this->level->setBlock($this, $this, true, true);
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if(!$this->isRedstoneSource()){
            $this->meta ^= 0x08;
            $this->level->setBlock($this, $this, true, false);
            $this->level->scheduleDelayedBlockUpdate($this, 30);
            $this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_REDSTONE_TRIGGER);
            RedstoneUtils::updateRedstone($this, null, true);
            RedstoneUtils::updateRedstone($this->getOppositeSidePosition(), null, true, $this->asPosition());
        }
        return true;
    }

    public function onUpdate(int $type){
        switch($type){
            case Level::BLOCK_UPDATE_SCHEDULED:
                if($this->isRedstoneSource()){
                    $this->meta ^= 0x08;
                    $this->level->setBlock($this, $this, true, false);
                    $this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_REDSTONE_TRIGGER);
                    RedstoneUtils::updateRedstone($this, null, true);
                    RedstoneUtils::updateRedstone($this->getOppositeSidePosition(), null, true, $this->asPosition());
                }
                break;
        }
    }

    public function isRedstoneSource(): bool{
        return (($this->meta & 0x08) === 0x08);
    }


    public function getRedstonePower(): int{
        return 15;
    }

    public function getOppositeSidePosition(){
        $side = self::getOppositeSide($this->isRedstoneSource() ? $this->meta ^ 0x08 : $this->meta);
        return Position::fromObject($this->asVector3()->getSide($side), $this->level);
    }
}
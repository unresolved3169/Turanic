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

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class LingeringPotion extends Projectile {

    const NETWORK_ID = self::LINGERING_POTION;

    const DATA_POTION_ID = 16;

    public $width = 0.25;
    public $length = 0.25;
    public $height = 0.25;
    protected $gravity = 0.1;
    protected $drag = 0.05;

    public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
        if(!isset($nbt->PotionId)){
            $nbt->setShort("PotionId", Potion::AWKWARD);
        }
        parent::__construct($level, $nbt, $shootingEntity);
        unset($this->dataProperties[self::DATA_SHOOTER_ID]);
        $this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_SHORT, $this->getPotionId());
        $this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER);
    }

    public function getPotionId() : int{
        return $this->namedtag->getShort("PotionId", Potion::AWKWARD);
    }

    public function onUpdate(int $currentTick){
        if($this->closed){
            return false;
        }

        if($this->age < 2){
            $aec = null;
            $potionId = $this->getPotionId();
            $color = Potion::getColor($potionId);

            $nbt = new CompoundTag("", [
                new ListTag("Pos", [
                    new DoubleTag("", $this->getX()),
                    new DoubleTag("", $this->getY()),
                    new DoubleTag("", $this->getZ()),
                ]),
                new ListTag("Motion", [
                    new DoubleTag("", 0),
                    new DoubleTag("", 0),
                    new DoubleTag("", 0),
                ]),
                new ListTag("Rotation", [
                    new FloatTag("", 0),
                    new FloatTag("", 0),
                ]),
                new IntTag("Age", 0),
                new ShortTag("PotionId", $potionId),
                new FloatTag("Radius", 3),
                new FloatTag("RadiusOnUse", -0.5),
                new FloatTag("RadiusPerTick", -0.005),
                new IntTag("WaitTime", 10),
                new IntTag("Duration", 600),
                new IntTag("DurationOnUse", 0),
                new IntArrayTag("Color", $color)
            ]);

            $aec = Entity::createEntity("AreaEffectCloud", $this->getLevel(), $nbt);
            if($aec instanceof Entity){
                $aec->spawnToAll();
            }
        }else{
            $this->close();
        }

        return parent::onUpdate($currentTick);
    }

    public function spawnTo(Player $player){
        $pk = new AddEntityPacket();
        $pk->type = LingeringPotion::NETWORK_ID;
        $pk->entityRuntimeId = $this->getId();
        $pk->position = $this;
        $pk->motion = $this->getMotion();
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }

    public function onCollideWithPlayer(Player $player): bool{
        return false;
    }

}
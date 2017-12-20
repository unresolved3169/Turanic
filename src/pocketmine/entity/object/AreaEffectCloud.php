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

namespace pocketmine\entity\object;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Potion;
use pocketmine\level\particle\Particle;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class AreaEffectCloud extends Entity {
    const NETWORK_ID = self::AREA_EFFECT_CLOUD;

    public $width = 5;
    public $length = 5;
    public $height = 1;

    private $PotionId = 0;
    private $Radius = 3;
    private $RadiusOnUse = -0.5;
    private $RadiusPerTick = -0.005;
    private $WaitTime = 10;
    private $Duration = 600;
    private $DurationOnUse = 0;

    public function initEntity(){
        parent::initEntity();

        if(!isset($this->namedtag->PotionId) or !($this->namedtag->PotionId instanceof ShortTag)){
            $this->namedtag->PotionId = new ShortTag("PotionId", $this->PotionId);
        }
        $this->PotionId = $this->namedtag->PotionId->getValue();

        if(!isset($this->namedtag->Radius) or !($this->namedtag->Radius instanceof FloatTag)){
            $this->namedtag->Radius = new FloatTag("Radius", $this->Radius);
        }
        $this->Radius = $this->namedtag->Radius->getValue();

        if(!isset($this->namedtag->RadiusOnUse) or !($this->namedtag->RadiusOnUse instanceof FloatTag)){
            $this->namedtag->RadiusOnUse = new FloatTag("RadiusOnUse", $this->RadiusOnUse);
        }
        $this->RadiusOnUse = $this->namedtag->RadiusOnUse->getValue();

        if(!isset($this->namedtag->RadiusPerTick) or !($this->namedtag->RadiusPerTick instanceof FloatTag)){
            $this->namedtag->RadiusPerTick = new FloatTag("RadiusPerTick", $this->RadiusPerTick);
        }
        $this->RadiusPerTick = $this->namedtag->RadiusPerTick->getValue();

        if(!isset($this->namedtag->WaitTime) or !($this->namedtag->WaitTime instanceof IntTag)){
            $this->namedtag->WaitTime = new IntTag("WaitTime", $this->WaitTime);
        }
        $this->WaitTime = $this->namedtag->WaitTime->getValue();

        if(!isset($this->namedtag->Duration) or !($this->namedtag->Duration instanceof IntTag)){
            $this->namedtag->Duration = new IntTag("Duration", $this->Duration);
        }
        $this->Duration = $this->namedtag->Duration->getValue();

        if(!isset($this->namedtag->DurationOnUse) or !($this->namedtag->DurationOnUse instanceof IntTag)){
            $this->namedtag->DurationOnUse = new IntTag("DurationOnUse", $this->DurationOnUse);
        }
        $this->DurationOnUse = $this->namedtag->DurationOnUse->getValue();

        $this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_PARTICLE_ID, self::DATA_TYPE_INT, Particle::TYPE_MOB_SPELL);//todo
        $this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_RADIUS, self::DATA_TYPE_FLOAT, $this->Radius);
        $this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_WAITING, self::DATA_TYPE_INT, $this->WaitTime);
        $this->setDataProperty(self::DATA_BOUNDING_BOX_HEIGHT, self::DATA_TYPE_FLOAT, 1);
        $this->setDataProperty(self::DATA_BOUNDING_BOX_WIDTH, self::DATA_TYPE_FLOAT, $this->Radius * 2);
        $this->setDataProperty(self::DATA_POTION_AMBIENT, self::DATA_TYPE_BYTE, 1);
    }

    public function onUpdate(int $tick){
        if($this->closed){
            return false;
        }

        $this->timings->startTiming();

        $hasUpdate = parent::onUpdate($tick);

        if($this->age > $this->Duration || $this->PotionId == 0 || $this->Radius <= 0){
            $this->close();
            $hasUpdate = true;
        }else{
            $effects = Potion::getEffectsById($this->PotionId);
            if(count($effects) <= 0){
                $this->close();
                $this->timings->stopTiming();

                return true;
            }
            /** @var Effect[] $effects */
            $firsteffect = $effects[0]; //Todo multiple effects
            $this->setDataProperty(self::DATA_POTION_COLOR, self::DATA_TYPE_INT, ((255 & 0xff) << 24) | (($firsteffect->getColor()[0] & 0xff) << 16) | (($firsteffect->getColor()[1] & 0xff) << 8) | ($firsteffect->getColor()[2] & 0xff));
            $this->Radius += $this->RadiusPerTick;
            $this->setDataProperty(self::DATA_BOUNDING_BOX_WIDTH, self::DATA_TYPE_FLOAT, $this->Radius * 2);
            if($this->WaitTime > 0){
                $this->WaitTime--;
                $this->timings->stopTiming();

                return true;
            }
            $bb = new AxisAlignedBB($this->x - $this->Radius, $this->y, $this->z - $this->Radius, $this->x + $this->Radius, $this->y + $this->height, $this->z + $this->Radius);
            $used = false;
            foreach($this->getLevel()->getCollidingEntities($bb, $this) as $collidingEntity){
                if($collidingEntity instanceof Living && $collidingEntity->distanceSquared($this) <= $this->Radius ** 2){
                    $used = true;
                    foreach($effects as $eff){
                        $collidingEntity->addEffect($eff);
                    }
                }
            }
            if($used){
                $this->Duration -= $this->DurationOnUse;
                $this->Radius += $this->RadiusOnUse;
                $this->WaitTime = 10;
            }
        }

        $this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_RADIUS, self::DATA_TYPE_FLOAT, $this->Radius);
        $this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_WAITING, self::DATA_TYPE_INT, $this->WaitTime);

        $this->timings->stopTiming();

        return $hasUpdate;
    }

    public function spawnTo(Player $player){
        $pk = new AddEntityPacket();
        $pk->type = AreaEffectCloud::NETWORK_ID;
        $pk->entityRuntimeId = $this->getId();
        $pk->position = $this;
        $pk->motion = $this->getMotion();
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }

    public function getName(){
        return "Area Effect Cloud";
    }
}
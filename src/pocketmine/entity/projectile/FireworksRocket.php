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

use pocketmine\item\FireworkRocket;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

class FireworksRocket extends Projectile{

    const NETWORK_ID = self::FIREWORKS_ROCKET;

    public $width = 0.25;
    public $height = 0.25;

    protected $gravity = 0.0;
    protected $drag = 0.01;

    /** @var int */
    protected $lifeTime = 0;

    /** @var FireworkRocket */
    protected $fireworks;

    public function __construct(Level $level, CompoundTag $nbt, $shootingEntity = null, FireworkRocket $item, $random = null){
        $this->fireworks = $item;
        $random = $random ?? new Random();

        $flyTime = 1;
        try{
            if (!is_null($nbt->getCompoundTag("Fireworks")))
                if ($nbt->getCompoundTag("Fireworks")->getByte("Flight", 1))
                    $flyTime = $nbt->getCompoundTag("Fireworks")->getByte("Flight", 1);
        } catch (\Exception $exception){
            $this->server->getLogger()->debug($exception);
        }

        $this->lifeTime = 20 * $flyTime + $random->nextBoundedInt(5) + $random->nextBoundedInt(7);

        $nbt->setInt("Life", $this->lifeTime);
        $nbt->setInt("LifeTime", $this->lifeTime);

        parent::__construct($level, $nbt, $shootingEntity);
    }

    protected function initEntity(){
        $this->setGenericFlag(self::DATA_FLAG_AFFECTED_BY_GRAVITY, true);
        $this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);
        $this->propertyManager->setItem(16, $this->fireworks);

        parent::initEntity();
    }

    public function spawnTo(Player $player){
        $this->setMotion($this->getDirectionVector());
        $this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BLAST);
        parent::spawnTo($player);
    }

    public function despawnFromAll(){
        $this->broadcastEntityEvent(EntityEventPacket::FIREWORK_PARTICLES, 0);

        parent::despawnFromAll();

        $this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LARGE_BLAST);
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        if ($this->lifeTime-- < 0){
            $this->flagForDespawn();
            return true;
        } else{
            return parent::entityBaseTick($tickDiff);
        }
    }

}
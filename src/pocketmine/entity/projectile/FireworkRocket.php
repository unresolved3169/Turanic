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

use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;

class FireworkRocket extends Projectile{

    const NETWORK_ID = self::FIREWORKS_ROCKET;

    public $width = 0.25;
    public $height = 0.25;

    protected $gravity = 0.0;
    protected $drag = 0.01;

    /** @var int */
    protected $lifeTime;

    public function __construct(Level $level, CompoundTag $nbt, $shootingEntity = null){
        if(!$nbt->hasTag("FireworksItem", CompoundTag::class)){
            $explosion = new CompoundTag("Explosion", [
                new ByteTag("Flicker", 1), // göz kırpma (Twinkle effect)
                new ByteTag("Trail", 1), // takip (Trail)
                new ByteTag("Type", 3), // 0 = Small Ball, 1 = Large Ball, 2 = Star-shaped, 3 = Creeper-shaped, 4 = Burst
                new IntArrayTag("Color", [255, 2, 36]), // rgb (kırmızı renk)
                new IntArrayTag("FadeColor", [255, 2, 36]), // rgb (kırmızı renk)
            ]);
            $nbt->setTag(new CompoundTag("FireworksItem", [
                new CompoundTag("tag", [
                    $explosion,
                    new CompoundTag("Fireworks", [
                        new ByteTag("Flight", 1), // -128 - 127
                        new ListTag("Explosions", [
                            $explosion
                        ], NBT::TAG_Compound),
                    ]),
                ])
            ]));
        }

        $time = (int) $nbt->getCompoundTag("FireworksItem")->getCompoundTag("tag")->getCompoundTag("Fireworks")->getByte("Flight", 1);

        if(!$nbt->hasTag("Life", IntTag::class)){
            $nbt->setInt("Life", $time * 20);
        }
        if(!$nbt->hasTag("LifeTime", IntTag::class)){
            $nbt->setInt("LifeTime", $time * 20 + mt_rand(0,5) + mt_rand(0,6));
        }
        $this->lifeTime = $nbt->getInt("LifeTime");
        parent::__construct($level, $nbt, $shootingEntity);
    }

    public function onUpdate(int $currentTick){
        if($this->closed){
            return false;
        }

        $this->timings->startTiming();

        if($this->lifeTime-- < 0){
            $pk = new EntityEventPacket();
            $pk->entityRuntimeId = $this->getId();
            $pk->event = EntityEventPacket::FIREWORK_PARTICLES;
            $pk->data = 0;
            Server::getInstance()->broadcastPacket($this->hasSpawned, $pk);

            $this->close();

            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LARGE_BLAST);
        }else{
            parent::onUpdate($currentTick);
        }

        $this->timings->stopTiming();
        return true;
    }

}
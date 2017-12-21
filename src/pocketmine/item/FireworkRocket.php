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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class FireworkRocket extends ProjectileItem {

    public function __construct($meta = 0, $count = 1){
        parent::__construct(self::FIREWORK, $meta, $count, "Firework Rocket");
    }

    public function getProjectileEntityType(): string{
        return "FireworkRocket";
    }

    public function getThrowForce(): float{
        return 1.1;
    }

    public function getMaxStackSize(): int{
        return 16;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null) : bool{
        return true;
    }

    public function canBeActivated(): bool{
        return true;
    }

    public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
        $up = $block->getSide(Vector3::SIDE_UP)->add(0.5, 0, 0.5);
        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $up->x),
                new DoubleTag("", $up->y),
                new DoubleTag("", $up->z)
            ], NBT::TAG_Double),
            new ListTag("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", $this->getThrowForce()),
                new DoubleTag("", 0)
            ], NBT::TAG_Double),
            new ListTag("Rotation", [
                new FloatTag("", mt_rand(0, 360)),
                new FloatTag("", -1*(float) (90.0 + (5.0 - 5.0/2)))
            ], NBT::TAG_Float),
        ]);

        $projectile = Entity::createEntity($this->getProjectileEntityType(), $player->getLevel(), $nbt, $player);

        if($projectile instanceof Projectile) {
            $player->getServer()->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($projectile));
            if ($projectileEv->isCancelled()) {
                $projectile->kill();
                return false;
            }
        }
        $player->getLevel()->broadcastLevelSoundEvent($up, LevelSoundEventPacket::SOUND_BLAST);
        if ($player->isSurvival()) {
            $item = $player->getItemInHand();
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
        }
        $projectile->spawnToAll();

        return true;
    }
}
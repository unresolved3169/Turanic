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
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\level\particle\MobSpellParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\Server;

class Arrow extends Projectile {
	const NETWORK_ID = self::ARROW;

	public $width = 0.5;
	public $height = 0.5;

	protected $gravity = 0.05;
	protected $drag = 0.01;
	
	protected $sound = true;
	protected $potionId = 0;
    protected $damage = 2;

	/**
	 * Arrow constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param Entity|null $shootingEntity
	 * @param bool        $critical
	 */
	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, bool $critical = false){
		if(!isset($nbt->Potion)){
			$nbt->setShort("Potion", 0);
		}
		parent::__construct($level, $nbt, $shootingEntity);
		$this->potionId = $this->namedtag->getShort("Potion", 0);
        $this->setCritical($critical);
	}

    public function isCritical() : bool{
        return $this->getGenericFlag(self::DATA_FLAG_CRITICAL);
    }

    public function setCritical(bool $value = true){
        $this->setGenericFlag(self::DATA_FLAG_CRITICAL, $value);
    }

    public function getResultDamage() : int{
        $base = parent::getResultDamage();
        if($this->isCritical()){
            return ($base + mt_rand(0, (int) ($base / 2) + 1));
        }else{
            return $base;
        }
    }

	/**
	 * @return int
	 */
	public function getPotionId() : int{
		return $this->potionId;
	}

    public function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->closed){
            return false;
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if($this->onGround or $this->hadCollision){
            $this->setCritical(false);
            if($this->sound === true and $this->level !== null){ //Prevents error of $this->level returning null
                $this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BOW_HIT);
                $this->sound = false;
            }
        }

        if($this->potionId != 0){
            if(!$this->onGround or ($this->onGround and (Server::getInstance()->getTick() % 4) == 0)){
                $color = Potion::getColor($this->potionId - 1)->toArray();
                $this->level->addParticle(new MobSpellParticle($this->add(
                    $this->width / 2 + mt_rand(-100, 100) / 500,
                    $this->height / 2 + mt_rand(-100, 100) / 500,
                    $this->width / 2 + mt_rand(-100, 100) / 500), $color[0], $color[1], $color[2]));
            }
            $hasUpdate = true;
        }

        if($this->age > 1200){
            $this->flagForDespawn();
            $hasUpdate = true;
        }

        return $hasUpdate;
    }

	public function onCollideWithPlayer(Player $player){
        if(!$this->hadCollision){
            return;
        }

        $item = Item::get(Item::ARROW);

        $playerInventory = $player->getInventory();
        if($player->isSurvival() and !$playerInventory->canAddItem($item)){
            return;
        }

        $this->server->getPluginManager()->callEvent($ev = new InventoryPickupArrowEvent($playerInventory, $this));
        if($ev->isCancelled()){
            return;
        }

        $pk = new TakeItemEntityPacket();
        $pk->eid = $player->getId();
        $pk->target = $this->getId();
        $this->server->broadcastPacket($this->getViewers(), $pk);

        $playerInventory->addItem(clone $item);
        $this->flagForDespawn();
	}
}

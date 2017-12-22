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

namespace pocketmine\entity\object;

use pocketmine\block\Anvil;
use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\block\SnowLayer;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class FallingSand extends Entity {
	const NETWORK_ID = self::FALLING_BLOCK;

	public $width = 0.98;
	public $height = 0.98;

	protected $gravity = 0.04;
	protected $drag = 0.02;
	protected $blockId = 0;
	protected $damage;

	public $canCollide = false;

	protected function initEntity(){
		parent::initEntity();
        if($this->namedtag->hasTag("TileID", IntTag::class)){
            $this->blockId = $this->namedtag->getInt("TileID");
        }elseif($this->namedtag->hasTag("Tile", ByteTag::class)) {
            $this->blockId = $this->namedtag->getByte("Tile");
            $this->namedtag->removeTag("Tile");
        }

		if($this->blockId === 0){
			$this->close();
			return;
		}

        $this->damage = $this->namedtag->getByte("Data", 0);

		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->getBlock() | ($this->getDamage() << 8));
	}

	/**
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function canCollideWith(Entity $entity){
		return false;
	}

    /**
     * @param EntityDamageEvent $source
     * @return bool|void
     * @internal param float $damage
     */
	public function attack(EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($source);
		}
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick){

		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0 and !$this->justCreated){
			return true;
		}

		$this->lastUpdate = $currentTick;

		$height = $this->fallDistance;

		$hasUpdate = $this->entityBaseTick($tickDiff);

		if($this->isAlive()){
			$pos = (new Vector3($this->x - 0.5, $this->y, $this->z - 0.5))->round();

			if($this->ticksLived === 1){
				$block = $this->level->getBlock($pos);
				if($block->getId() !== $this->blockId){
					return true;
				}
				$this->level->setBlock($pos, Block::get(0), true);
			}

			$this->motionY -= $this->gravity;

			$this->move($this->motionX, $this->motionY, $this->motionZ);

			$friction = 1 - $this->drag;

			$this->motionX *= $friction;
			$this->motionY *= 1 - $this->drag;
			$this->motionZ *= $friction;

			$pos = (new Vector3($this->x - 0.5, $this->y, $this->z - 0.5))->round();

			if($this->onGround){
				$this->kill();
				$block = $this->level->getBlock($pos);
				if($block->getId() > 0 and !$block->isSolid() and !($block instanceof Liquid)){
					$this->getLevel()->dropItem($this, ItemItem::get($this->getBlock(), $this->getDamage(), 1));
				}else{
					if($block instanceof SnowLayer){
						$oldDamage = $block->getDamage();
						$this->server->getPluginManager()->callEvent($ev = new EntityBlockChangeEvent($this, $block, Block::get($this->getBlock(), $this->getDamage() + $oldDamage)));
					}else{
						$this->server->getPluginManager()->callEvent($ev = new EntityBlockChangeEvent($this, $block, Block::get($this->getBlock(), $this->getDamage())));
					}

					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($pos, $ev->getTo(), true);
						if($ev->getTo() instanceof Anvil){
							$sound = new AnvilFallSound($this);
							$this->getLevel()->addSound($sound);
							foreach($this->level->getNearbyEntities($this->boundingBox->grow(0.1, 0.1, 0.1), $this) as $entity){
								$entity->scheduleUpdate();
								if(!$entity->isAlive()){
									continue;
								}
								if($entity instanceof Living){
									$damage = ($height - 1) * 2;
									if($damage > 40) $damage = 40;
									$ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageByEntityEvent::CAUSE_FALL, $damage, 0.1);
									$entity->attack($ev);
								}
							}

						}
					}
				}
				$hasUpdate = true;
			}

			$this->updateMovement();
		}

		return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
	}

	/**
	 * @return int
	 */
	public function getBlock(){
		return $this->blockId;
	}

	/**
	 * @return mixed
	 */
	public function getDamage(){
		return $this->damage;
	}

	public function saveNBT(){
		$this->namedtag->setInt("TileID", $this->blockId, true);
		$this->namedtag->setByte("Data", $this->damage);
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = FallingSand::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}
}

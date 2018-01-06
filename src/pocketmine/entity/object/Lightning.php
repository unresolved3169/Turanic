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

use pocketmine\block\Liquid;
use pocketmine\entity\hostile\Creeper;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ExplodePacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;

// TODO : OPTIMIZATION
class Lightning extends Entity {
	const NETWORK_ID = self::LIGHTNING_BOLT;

    public $width = 0.3;
    public $height = 1.8;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Lightning";
	}

	public function initEntity(){
		parent::initEntity();
		$this->setMaxHealth(2);
		$this->setHealth(2);
	}

	/**
	 * @param $tick
	 *
	 * @return bool
	 */
	public function onUpdate(int $tick){
	    // TODO
	    $this->age++;
        $this->lastUpdate = $tick;
        if($this->age > 6 * 20){
            $this->close();
        }
        return true;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
        parent::spawnTo($player);

		$pk2 = new ExplodePacket();
		$pk2->position = $this->asVector3();
		$pk2->radius = 10;
		$pk2->records = [];
		$player->dataPacket($pk2);
	}

	public function spawnToAll(){
		parent::spawnToAll();

		if($this->getLevel()->getServer()->lightningFire){
			$fire = ItemItem::get(ItemItem::FIRE)->getBlock();
			$oldBlock = $this->getLevel()->getBlock($this);
			if($oldBlock instanceof Liquid){

			}elseif($oldBlock->isSolid()){
				$v3 = new Vector3($this->x, $this->y + 1, $this->z);
			}else{
				$v3 = new Vector3($this->x, $this->y, $this->z);
			}
			if(isset($v3)) $this->getLevel()->setBlock($v3, $fire);

			foreach($this->level->getNearbyEntities($this->boundingBox->grow(4, 3, 4), $this) as $entity){
				if($entity instanceof Player){
					$damage = mt_rand(8, 20);
					$ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageByEntityEvent::CAUSE_LIGHTNING, $damage);
                    $entity->attack($ev);
					$entity->setOnFire(mt_rand(3, 8));
				}

				if($entity instanceof Creeper){
					$entity->setPowered(true, $this);
				}
			}

            $spk = new PlaySoundPacket();
            $spk->soundName = "ambient.weather.lightning.impact";
            $spk->x = $this->getX();
            $spk->y = $this->getY();
            $spk->z = $this->getZ();
            $spk->volume = 500;
            $spk->pitch = 1;

            Server::getInstance()->broadcastPacket($this->getLevel()->getPlayers(), $spk);
		}
	}
}
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

use pocketmine\block\Block;
use pocketmine\entity\Hanging;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\network\mcpe\protocol\AddPaintingPacket;
use pocketmine\Player;

class Painting extends Hanging {
	const NETWORK_ID = self::PAINTING;

	const TAG_MOTIVE = "Motive";

	/** @var int */
	private $motive;

	public function initEntity(){
		$this->setMaxHealth(1);
		parent::initEntity();

		if($this->namedtag->hasTag(self::TAG_MOTIVE)){
			$this->motive = $this->namedtag->getString(self::TAG_MOTIVE);
		}else $this->close();
	}

    /**
     * @param EntityDamageEvent $source
     * @return bool
     * @internal param float $damage
     */
	public function attack(EntityDamageEvent $source){
		parent::attack($source);
		if($source->isCancelled()) return false;
		$this->level->addParticle(new DestroyBlockParticle($this->add(0.5), Block::get(Block::LADDER)));
		$this->kill();
		return true;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddPaintingPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->direction = $this->getDirection();
		$pk->title = $this->motive;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	protected function updateMovement(){
		//Nothing to update, paintings cannot move.
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		return [ItemItem::get(ItemItem::PAINTING, 0, 1)];
	}
}
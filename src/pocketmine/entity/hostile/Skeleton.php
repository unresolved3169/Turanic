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

namespace pocketmine\entity\hostile;

use pocketmine\entity\Monster;
use pocketmine\entity\projectile\ProjectileSource;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Skeleton extends Monster implements ProjectileSource {
	const NETWORK_ID = self::SKELETON;

	public $drag = 0.2;
	public $gravity = 0.3;
	
	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
        $this->setMaxHealth(20);
		parent::initEntity();
	}
	/**
	 * @return string
	 */
	public function getName() : string{
		return "Skeleton";
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		parent::spawnTo($player);

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->item = new ItemItem(ItemItem::BOW);
		$pk->inventorySlot = $pk->hotbarSlot = 0;

		$player->dataPacket($pk);
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$drops = [
			ItemItem::get(ItemItem::ARROW, 0, mt_rand(0, 2))
		];
		$drops[] = ItemItem::get(ItemItem::BONE, 0, mt_rand(0, 2));

		return $drops;
	}

    public function getXpDropAmount(): int{
        return 5;
    }
}

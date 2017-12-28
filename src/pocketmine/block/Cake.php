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

namespace pocketmine\block;

use pocketmine\entity\Effect;
use pocketmine\entity\Living;
use pocketmine\item\FoodSource;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Cake extends Transparent implements FoodSource {

	protected $id = self::CAKE_BLOCK;

	/**
	 * Cake constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 0.5;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Cake Block";
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){

        $f = $this->getDamage() * 0.125; //1 slice width

        return new AxisAlignedBB(
            $this->x + 0.0625 + $f,
            $this->y,
            $this->z + 0.0625,
            $this->x + 1 - 0.0625,
            $this->y + 0.5,
            $this->z + 1 - 0.0625
        );
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(Vector3::SIDE_DOWN);
		if($down->getId() !== self::AIR){
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}

		return false;
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(Vector3::SIDE_DOWN)->getId() === self::AIR){ //Replace with common break method
				$this->getLevel()->setBlock($this, Block::get(Block::AIR), true);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		return [];
	}

	/**
	 * @param Item        $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null){
        if($player !== null){
            $player->consumeObject($this);
            return true;
  		}

		return false;
	}

	public function requiresHunger(): bool{
        return true;
    }

    /**
	 * @return int
	 */
	public function getFoodRestore() : int{
		return 2;
	}

	/**
	 * @return float
	 */
	public function getSaturationRestore() : float{
		return 0.4;
	}

	/**
	 * @return Air|Cake
	 */
	public function getResidue(){
		$clone = clone $this;
		$clone->meta++;
		if($clone->meta > 0x06){
			$clone = Block::get(Block::AIR);
		}
		return $clone;
	}

	/**
	 * @return Effect[]
	 */
	public function getAdditionalEffects() : array{
		return [];
	}

	public function onConsume(Living $consumer){
        $this->level->setBlock($this, $this->getResidue());
    }
}

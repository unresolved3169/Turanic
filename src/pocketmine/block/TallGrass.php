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

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class TallGrass extends Flowable{

	const NORMAL = 1;
	const FERN = 2;

	protected $id = self::TALL_GRASS;

	public function __construct(int $meta = 1){
		$this->meta = $meta;
	}

	public function canBeReplaced() : bool{
		return true;
	}

	public function getName() : string{
		static $names = [
			0 => "Dead Shrub",
			1 => "Tall Grass",
			2 => "Fern"
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}

	public function getBurnChance() : int{
		return 60;
	}

	public function getBurnAbility() : int{
		return 100;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$down = $this->getSide(0);
		if($down->getId() === self::GRASS){
			$this->getLevel()->setBlock($blockReplace, $this, true);

			return true;
		}

		return false;
	}

	public function onUpdate(int $type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->isTransparent() === true){ //Replace with common break method
				$this->getLevel()->setBlock($this, new Air(), false, false);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_SHEARS;
	}

	public function getDrops(Item $item) : array{
		if(mt_rand(0, 15) === 0){
			return [
				Item::get(Item::WHEAT_SEEDS)
			];
		}

		return [];
	}

}

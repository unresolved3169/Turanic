<?php

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\Player;

class TallGrass extends Flowable {

	const NORMAL = 1;
	const FERN = 2;

	protected $id = self::TALL_GRASS;

	/**
	 * TallGrass constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 1){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeReplaced(){
		return true;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		static $names = [
			0 => "Dead Shrub",
			1 => "Tall Grass",
			2 => "Fern"
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}

	/**
	 * @return int
	 */
	public function getBurnChance() : int{
		return 60;
	}

	/**
	 * @return int
	 */
	public function getBurnAbility() : int{
		return 100;
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
		$down = $this->getSide(0);
		if($down->getId() === self::GRASS){
			$this->getLevel()->setBlock($block, $this, true);

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
			if($this->getSide(0)->isTransparent() === true){ //Replace with common break method
				$this->getLevel()->setBlock($this, new Air(), false, false);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_SHEARS;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		if(mt_rand(0, 15) === 0){
			return [
				[Item::WHEAT_SEEDS, 0, 1]
			];
		}

		return [];
	}

}

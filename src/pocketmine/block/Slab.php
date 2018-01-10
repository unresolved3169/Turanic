<?php

/*
 *
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
 *
*/

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

// TODO : UPDATE!?!?!?
class Slab extends Transparent {

	const STONE = 0;
	const SANDSTONE = 1;
	const WOODEN = 2;
	const COBBLESTONE = 3;
	const BRICK = 4;
	const STONE_BRICK = 5;
	const QUARTZ = 6;
	const NETHER_BRICK = 7;

	protected $id = self::SLAB;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getName() : string{
		static $names = [
			0 => "Stone",
			1 => "Sandstone",
			2 => "Wooden",
			3 => "Cobblestone",
			4 => "Brick",
			5 => "Stone Brick",
			6 => "Quartz"
		];
		return (($this->meta & 0x08) > 0 ? "Upper " : "") . ($names[$this->getVariant()] ?? "") . " Slab";
	}

	/**
	 * @return int
	 */
	public function getBurnChance() : int{
		$type = $this->meta & 0x07;
		if($type == self::WOODEN){
			return 5;
		}
		return 0;
	}

	/**
	 * @return int
	 */
	public function getBurnAbility() : int{
		$type = $this->meta & 0x07;
		if($type == self::WOODEN){
			return 5;
		}
		return 0;
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){

		if(($this->meta & 0x08) > 0){
			return new AxisAlignedBB(
				$this->x,
				$this->y + 0.5,
				$this->z,
				$this->x + 1,
				$this->y + 1,
				$this->z + 1
			);
		}else{
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + 0.5,
				$this->z + 1
			);
		}
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$this->meta &= 0x07;
		if($face === 0){
			if($blockClicked->getId() === self::SLAB and ($blockClicked->getDamage() & 0x08) === 0x08 and ($blockClicked->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($blockClicked, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

				return true;
			}elseif($blockReplace->getId() === self::SLAB and ($blockReplace->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($blockReplace, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

				return true;
			}else{
				$this->meta |= 0x08;
			}
		}elseif($face === 1){
			if($blockClicked->getId() === self::SLAB and ($blockClicked->getDamage() & 0x08) === 0 and ($blockClicked->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($blockClicked, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

				return true;
			}elseif($blockReplace->getId() === self::SLAB and ($blockReplace->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($blockReplace, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

				return true;
			}
			//TODO: check for collision
		}else{
			if($blockReplace->getId() === self::SLAB){
				if(($blockReplace->getDamage() & 0x07) === ($this->meta & 0x07)){
					$this->getLevel()->setBlock($blockReplace, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

					return true;
				}

				return false;
			}else{
				if($clickVector->y > 0.5){
					$this->meta |= 0x08;
				}
			}
		}

		if($blockReplace->getId() === self::SLAB and ($blockClicked->getDamage() & 0x07) !== ($this->meta & 0x07)){
			return false;
		}
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		return true;
	}

	public function getVariantBitmask(): int{
        return 0x07;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_WOODEN;
    }

	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}

    public function canBePlacedAt(Block $blockReplace, Vector3 $clickVector, int $face, bool $isClickedBlock): bool{
        if (parent::canBePlacedAt($blockReplace, $clickVector, $face, $isClickedBlock))
            return true;

        if ($blockReplace->getId() === $this->getId() and $blockReplace->getVariant() === $this->getVariant()) {
            if (($blockReplace->getDamage() & 0x08) !== 0) { //Trying to combine with top slab
                return $clickVector->y <= 0.5 or (!$isClickedBlock and $face === Vector3::SIDE_UP);
            } else {
                return $clickVector->y >= 0.5 or (!$isClickedBlock and $face === Vector3::SIDE_DOWN);
            }
        }

        return false;
    }
}
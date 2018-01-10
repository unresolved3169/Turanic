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
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class WoodSlab extends Transparent {

	protected $id = self::WOOD_SLAB;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getName() : string{
		static $names = [
			0 => "Oak",
			1 => "Spruce",
			2 => "Birch",
			3 => "Jungle",
			4 => "Acacia",
			5 => "Dark Oak"
		];
		return (($this->meta & 0x08) === 0x08 ? "Upper " : "") . ($names[$this->getVariant()] ?? "") . " Wooden Slab";
	}

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
            if($blockClicked->getId() === $this->id and ($blockClicked->getDamage() & 0x08) === 0x08 and $blockClicked->getVariant() === $this->getVariant()){
                $this->getLevel()->setBlock($blockClicked, Block::get(self::DOUBLE_WOODEN_SLAB, $this->getVariant()), true);

				return true;
			}elseif($blockReplace->getId() === self::WOOD_SLAB and $blockReplace->getVariant() === $this->getVariant()){
				$this->getLevel()->setBlock($blockReplace, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

				return true;
			}else{
				$this->meta |= 0x08;
			}
		}elseif($face === 1){
			if($blockClicked->getId() === self::WOOD_SLAB and ($blockClicked->getDamage() & 0x08) === 0 and $blockClicked->getVariant() === $this->getVariant()){
				$this->getLevel()->setBlock($blockClicked, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

				return true;
			}elseif($blockReplace->getId() === self::WOOD_SLAB and $blockReplace->getVariant() === $this->getVariant()){
				$this->getLevel()->setBlock($blockReplace, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

				return true;
			}
		}else{ //TODO: collision
			if($blockReplace->getId() === self::WOOD_SLAB){
				if($blockReplace->getVariant() === $this->meta){
					$this->getLevel()->setBlock($blockReplace, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

					return true;
				}

				return false;
			}else{
				if($clickVector->y > 0.5){
					$this->meta |= 0x08;
				}
			}
		}

		if($blockReplace->getId() === self::WOOD_SLAB and $blockClicked->getVariant() !== $this->getVariant()){
			return false;
		}
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		return true;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function getVariantBitmask(): int{
        return 0x07;
    }

    public function getFuelTime(): int{
        return 300;
    }
}
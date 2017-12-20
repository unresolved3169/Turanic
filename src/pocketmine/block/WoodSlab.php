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
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class WoodSlab extends Transparent {

	protected $id = self::WOOD_SLAB;

	/**
	 * WoodSlab constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 2;
	}

	/**
	 * @return string
	 */
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
		$this->meta &= 0x07;
		if($face === 0){
            if($target->getId() === $this->id and ($target->getDamage() & 0x08) === 0x08 and $target->getVariant() === $this->getVariant()){
                $this->getLevel()->setBlock($target, Block::get(self::DOUBLE_WOODEN_SLAB, $this->getVariant()), true);

				return true;
			}elseif($block->getId() === self::WOOD_SLAB and $block->getVariant() === $this->getVariant()){
				$this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

				return true;
			}else{
				$this->meta |= 0x08;
			}
		}elseif($face === 1){
			if($target->getId() === self::WOOD_SLAB and ($target->getDamage() & 0x08) === 0 and $target->getVariant() === $this->getVariant()){
				$this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

				return true;
			}elseif($block->getId() === self::WOOD_SLAB and $block->getVariant() === $this->getVariant()){
				$this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

				return true;
			}
		}else{ //TODO: collision
			if($block->getId() === self::WOOD_SLAB){
				if($block->getVariant() === $this->meta){
					$this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_WOOD_SLAB, $this->getVariant()), true);

					return true;
				}

				return false;
			}else{
				if($fy > 0.5){
					$this->meta |= 0x08;
				}
			}
		}

		if($block->getId() === self::WOOD_SLAB and $target->getVariant() !== $this->getVariant()){
			return false;
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		return [
			[$this->id, $this->meta & 0x07, 1],
		];
	}

	public function getFuelTime(): int{
        return 300;
    }
}
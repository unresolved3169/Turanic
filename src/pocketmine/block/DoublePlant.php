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

use pocketmine\item\Tool;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DoublePlant extends Flowable {
    const BITFLAG_TOP = 0x08;

	protected $id = self::DOUBLE_PLANT;

	const SUNFLOWER = 0;
	const LILAC = 1;
	const DOUBLE_TALLGRASS = 2;
	const LARGE_FERN = 3;
	const ROSE_BUSH = 4;
	const PEONY = 5;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function canBeReplaced() : bool{
        return $this->meta === 2 or $this->meta === 3; //grass or fern
	}

	public function getName() : string{
		static $names = [
			0 => "Sunflower",
			1 => "Lilac",
			2 => "Double Tallgrass",
			3 => "Large Fern",
			4 => "Rose Bush",
			5 => "Peony"
		];
		return $names[$this->getVariant()] ?? "";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        $id = $blockReplace->getSide(Vector3::SIDE_DOWN)->getId();
        if(($id === Block::GRASS or $id === Block::DIRT) and $blockReplace->getSide(Vector3::SIDE_UP)->canBeReplaced()){
            $this->getLevel()->setBlock($blockReplace, $this, false, false);
            $this->getLevel()->setBlock($blockReplace->getSide(Vector3::SIDE_UP), Block::get($this->id, $this->meta | self::BITFLAG_TOP), false, false);

            return true;
        }

        return false;
	}

    /**
     * Returns whether this double-plant has a corresponding other half.
     * @return bool
     */
    public function isValidHalfPlant() : bool{
        if($this->meta & self::BITFLAG_TOP){
            $other = $this->getSide(Vector3::SIDE_DOWN);
        }else{
            $other = $this->getSide(Vector3::SIDE_UP);
        }

        return (
            $other->getId() === $this->getId() and
            $other->getVariant() === $this->getVariant() and
            ($other->getDamage() & self::BITFLAG_TOP) !== ($this->getDamage() & self::BITFLAG_TOP)
        );
    }

    public function onUpdate($type){
        if($type === Level::BLOCK_UPDATE_NORMAL){
            $down = $this->getSide(Vector3::SIDE_DOWN);
            if(!$this->isValidHalfPlant() or (($this->meta & self::BITFLAG_TOP) === 0 and $down->isTransparent())){
                $this->getLevel()->useBreakOn($this);

                return Level::BLOCK_UPDATE_NORMAL;
            }
        }

        return false;
    }

	/**
	 * @param Item $item
	 *
	 * @return mixed|void
	 */
	public function onBreak(Item $item){
		$up = $this->getSide(1);
		$down = $this->getSide(0);
		if(($this->meta & 0x08) === 0x08){ // This is the Top part of flower
			if($up->getId() === $this->id and $up->meta !== 0x08){ // Checks if the block ID and meta are right
				$this->getLevel()->setBlock($up, new Air(), true, true);
			}elseif($down->getId() === $this->id and $down->meta !== 0x08){
				$this->getLevel()->setBlock($down, new Air(), true, true);
			}
		}else{ // Bottom Part of flower
			if($up->getId() === $this->id and ($up->meta & 0x08) === 0x08){
				$this->getLevel()->setBlock($up, new Air(), true, true);
			}elseif($down->getId() === $this->id and ($down->meta & 0x08) === 0x08){
				$this->getLevel()->setBlock($down, new Air(), true, true);
			}
		}
	}

    public function getVariantBitmask() : int{
        return 0x07;
    }

    public function getToolType() : int{
        return ($this->meta === 2 or $this->meta === 3) ? Tool::TYPE_SHEARS : Tool::TYPE_NONE;
    }

    public function getToolHarvestLevel() : int{
        return ($this->meta === 2 or $this->meta === 3) ? 1 : 0; //only grass or fern require shears
    }

	public function getDrops(Item $item) : array{
        if($this->meta & self::BITFLAG_TOP){
            if($this->isCompatibleWithTool($item)){
                return parent::getDrops($item);
            }

            if(mt_rand(0, 24) === 0){
                return [
                    [Item::SEEDS, 0, 1]
                ];
            }
        }

        return [];
	}

    public function getAffectedBlocks() : array{
        if($this->isValidHalfPlant()){
            return [$this, $this->getSide(($this->meta & self::BITFLAG_TOP) !== 0 ? Vector3::SIDE_DOWN : Vector3::SIDE_UP)];
        }

        return parent::getAffectedBlocks();
    }
}

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

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Vine extends Transparent {

    const FLAG_SOUTH = 0x01;
    const FLAG_WEST = 0x02;
    const FLAG_NORTH = 0x04;
    const FLAG_EAST = 0x08;

	protected $id = self::VINE;

	/**
	 * Vine constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function isSolid(){
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Vines";
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 0.2;
	}

	/**
	 * @return bool
	 */
	public function canPassThrough(){
		return true;
	}

	/**
	 * @return bool
	 */
	public function hasEntityCollision(){
		return true;
	}

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity){
		$entity->resetFallDistance();
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){
        $minX = 1;
        $minY = 1;
        $minZ = 1;
        $maxX = 0;
        $maxY = 0;
        $maxZ = 0;

        $flag = $this->meta > 0;

        if(($this->meta & self::FLAG_WEST) > 0){
            $maxX = max($maxX, 0.0625);
            $minX = 0;
            $minY = 0;
            $maxY = 1;
            $minZ = 0;
            $maxZ = 1;
            $flag = true;
        }

        if(($this->meta & self::FLAG_EAST) > 0){
            $minX = min($minX, 0.9375);
            $maxX = 1;
            $minY = 0;
            $maxY = 1;
            $minZ = 0;
            $maxZ = 1;
            $flag = true;
        }

        if(($this->meta & self::FLAG_SOUTH) > 0){
            $minZ = min($minZ, 0.9375);
            $maxZ = 1;
            $minX = 0;
            $maxX = 1;
            $minY = 0;
            $maxY = 1;
            $flag = true;
        }

        //TODO: Missing NORTH check
        if(!$flag and $this->getSide(Vector3::SIDE_UP)->isSolid()){
            $minY = min($minY, 0.9375);
            $maxY = 1;
            $minX = 0;
            $maxX = 1;
            $minZ = 0;
            $maxZ = 1;
        }

        return new AxisAlignedBB(
            $this->x + $minX,
            $this->y + $minY,
            $this->z + $minZ,
            $this->x + $maxX,
            $this->y + $maxY,
            $this->z + $maxZ
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
        if (!$target->isSolid() or $face === Vector3::SIDE_UP or $face === Vector3::SIDE_DOWN) {
            return false;
        }
        $faces = [
            Vector3::SIDE_NORTH => self::FLAG_SOUTH,
            Vector3::SIDE_SOUTH => self::FLAG_NORTH,
            Vector3::SIDE_WEST => self::FLAG_EAST,
            Vector3::SIDE_EAST => self::FLAG_WEST
        ];
        $this->meta = $faces[$face] ?? 0;
        if ($block->getId() === $this->getId()) {
            $this->meta |= $block->meta;
        }
        $this->getLevel()->setBlock($block, $this, true, true);
        return true;
    }

    public function onUpdate($type){
        if($type === Level::BLOCK_UPDATE_NORMAL){
            $sides = [
                self::FLAG_SOUTH => Vector3::SIDE_SOUTH,
                self::FLAG_WEST => Vector3::SIDE_WEST,
                self::FLAG_NORTH => Vector3::SIDE_NORTH,
                self::FLAG_EAST => Vector3::SIDE_EAST
            ];
            $meta = $this->meta;
            foreach($sides as $flag => $side){
                if(($meta & $flag) === 0){
                    continue;
                }
                if(!$this->getSide($side)->isSolid()){
                    $meta &= ~$flag;
                }
            }
            if($meta !== $this->meta){
                if($meta === 0){
                    $this->level->useBreakOn($this);
                }else{
                    $this->meta = $meta;
                    $this->level->setBlock($this, $this);
                }
                return Level::BLOCK_UPDATE_NORMAL;
            }
        }elseif($type === Level::BLOCK_UPDATE_RANDOM){
            //TODO: vine growth
        }
        return false;
    }

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		if($item->isShears()){
			return [
				[$this->id, 0, 1],
			];
		}else{
			return [];
		}
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_SHEARS;
	}

	public function canBeClimbed(): bool{
        return true;
    }
}
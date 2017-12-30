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
use pocketmine\tile\Skull as TileSkull;

use pocketmine\tile\Tile;

class Skull extends Flowable {

	protected $id = self::SKULL_BLOCK;

	/**
	 * SkullBlock constructor.
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
		return 1;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Mob Head";
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){
		$x1 = $x2 = $z1 = $z2 = 0;
		switch($this->meta){
            case 0:
            case 1:
                return new AxisAlignedBB(
                    $this->x + 0.25,
                    $this->y,
                    $this->z + 0.25,
                    $this->x + 0.75,
                    $this->y + 0.5,
                    $this->z + 0.75
                );
            case 2:
                $x1 = 0.25;
                $x2 = 0.75;
                $z1 = 0;
                $z2 = 0.5;
                break;
            case 3:
                $x1 = 0.5;
                $x2 = 1;
                $z1 = 0.25;
                $z2 = 0.75;
                break;
            case 4:
                $x1 = 0.25;
                $x2 = 0.75;
                $z1 = 0.5;
                $z2 = 1;
                break;
            case 5:
                $x1 = 0;
                $x2 = 0.5;
                $z1 = 0.25;
                $z2 = 0.75;
                break;
        }
		return new AxisAlignedBB(
			$this->x + $x1,
			$this->y + 0.25,
			$this->z + $z1,
			$this->x + $x2,
			$this->y + 0.75,
			$this->z + $z2
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
        if($face === Vector3::SIDE_DOWN){
            return false;
        }
        $this->meta = $face;
        $this->getLevel()->setBlock($block, $this, true);
        Tile::createTile(Tile::SKULL, $this->getLevel(), TileSkull::createNBT($this, $face, $item, $player));
        return true;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		$tile = $this->level->getTile($this);
		if($tile instanceof TileSkull){
			return [
				[Item::SKULL, $tile->getType(), 1]
			];
		}
		return [];
	}
}

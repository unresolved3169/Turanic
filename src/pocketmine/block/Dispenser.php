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

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Dispenser as TileDispenser;
use pocketmine\tile\Tile;

class Dispenser extends Solid {

	protected $id = self::DISPENSER;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 3.5;
	}

	public function getName() : string{
		return "Dispenser";
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$dispenser = null;
		if($player instanceof Player){
			$pitch = $player->getPitch();
			if(abs($pitch) >= 45){
				if($pitch < 0) $f = 4;
				else $f = 5;
			}else $f = $player->getDirection();
		}else $f = 0;
		$faces = [
			3 => 3,
			0 => 4,
			2 => 5,
			1 => 2,
			4 => 0,
			5 => 1
		];
		$this->meta = $faces[$f];

		$bool = $this->getLevel()->setBlock($blockReplace, $this, true, true);
		Tile::createTile(Tile::DISPENSER, $this->getLevel(), TileDispenser::createNBT($this, $face, $item, $player));

		return $bool;
	}

	public function activate(){
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof TileDispenser){
			$tile->activate();
		}
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			$t = $this->getLevel()->getTile($this);
			$dispenser = null;
			if($t instanceof TileDispenser){
				$dispenser = $t;
			}else{
				$dispenser = Tile::createTile(Tile::DISPENSER, $this->getLevel(), TileDispenser::createNBT($this));
			}

			$player->addWindow($dispenser->getInventory());
		}

		return true;
	}
}
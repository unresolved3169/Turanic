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
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Beacon as TileBeacon;
use pocketmine\tile\Tile;

class Beacon extends Transparent {

	protected $id = self::BEACON;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Beacon";
	}

	public function getLightLevel() : int{
		return 15;
	}

	public function getHardness() : float{
		return 3;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$bool = $this->getLevel()->setBlock($this, $this, true, true);
        Tile::createTile(Tile::BEACON, $this->getLevel(), TileBeacon::createNBT($this, $face, $item, $player));
		return $bool;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			$top = $this->getSide(Vector3::SIDE_UP);
			if($top->isTransparent() !== true){
				return true;
			}

			$t = $this->getLevel()->getTile($this);
			$beacon = null;
			if($t instanceof TileBeacon){
				$beacon = $t;
			}else{
				$beacon = Tile::createTile(Tile::BEACON, $this->getLevel(), TileBeacon::createNBT($this));
			}

			if($player->isCreative() and $player->getServer()->limitedCreative){
				return true;
			}
			$player->addWindow($beacon->getInventory());
		}

		return true;
	}

}
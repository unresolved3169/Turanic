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

use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\MusicDisc;
use pocketmine\tile\Jukebox as JukeboxTile;
use pocketmine\tile\Tile;

class Jukebox extends Solid {

	protected $id = self::JUKEBOX;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2;
	}

    public function getToolType() : int{
        return BlockToolType::TYPE_AXE;
    }

    public function getName() : string{
		return "Jukebox";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		Tile::createTile("Jukebox", $this->level, JukeboxTile::createNBT($this, $face, $item, $player));

		return true;
	}

	public function onActivate(Item $item, Player $player = null): bool{
	    $tile = $this->level->getTileAt($this->x, $this->y, $this->z);
	    if($tile instanceof JukeboxTile){
	        $tile->dropMusicDisc();
	        if($item instanceof MusicDisc){
	            if($player != null && !$player->isCreative()) $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
	            $tile->setRecordItem($item);
            }
        }

        return true;
	}
}
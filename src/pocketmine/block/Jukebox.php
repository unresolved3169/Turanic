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

use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\MusicDisc;
use pocketmine\tile\Jukebox as JukeboxTile;
use pocketmine\tile\Tile;

// TODO : Update
class Jukebox extends Solid {

	protected $id = self::JUKEBOX;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getName() : string{
		return "Jukebox";
	}

	public function getToolType() : int{
		return Tool::TYPE_AXE;
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
	
	public function playMusicDisc(Player $p = null) : bool{
		$record = $this->getRecord();
		if($record == 0) return false;
		
		$soundId = 90 + ($record - 2256); //  :D
		$this->level->broadcastLevelSoundEvent($this, $soundId);
		
		if($p instanceof Player){
			$name = $this->getMusicDisc()->getRecordName();
			$p->sendTip("§6Music Playing: §3C418 {$name}"); // TODO: send annoucement
		}
		
		return true;
	}
	
	public function onActivate(Item $item, Player $p = null){
		if($item instanceof MusicDisc){
			$this->setRecord($item->getRecordId());
			$this->setRecordItem($item);
			$this->playMusicDisc($p);
		}else{
			$this->setRecord(0);
			$this->setRecordItem(null);
		}
	}
	
	public function setRecord(int $record){
		$tile = $this->level->getTile($this);
		if($tile instanceof JukeboxTile){
			if($tile->getRecord() > 0){
				$this->level->dropItem($this->add(0,1,0), $tile->getRecordItem());
			}
			$tile->setRecord($record);
		}
	}
	
	public function getRecord() : int{
		$tile = $this->level->getTile($this);
		if($tile instanceof JukeboxTile){
			return $tile->getRecord();
		}
		return 0;
	}
	
	public function getMusicDisc(){
		$tile = $this->level->getTile($this);
		if($tile instanceof JukeboxTile){
			return $tile->getRecordItem();
		}
		return null;
	}
}
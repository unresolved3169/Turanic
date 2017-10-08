<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\block;

use pocketmine\item\Tool;

use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\MusicDisc;
use pocketmine\tile\Jukebox as JukeboxTile;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class Jukebox extends Solid {

	protected $id = self::JUKEBOX;

	/**
	 * Jukebox constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 2;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Jukebox";
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_AXE;
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
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevel()->setBlock($block, $this, true, true);
		
		$tile = JukeboxTile::createTileFromPosition("jukebox", $this);

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
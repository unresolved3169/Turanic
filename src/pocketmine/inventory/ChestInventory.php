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

namespace pocketmine\inventory;

use pocketmine\block\TrappedChest;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\Chest;

class ChestInventory extends ContainerInventory{

	/** @var Chest */
	protected $holder;

	/**
	 * @param Chest $tile
	 */
	public function __construct(Chest $tile){
		parent::__construct($tile);
	}

	public function getNetworkType() : int{
		return WindowTypes::CONTAINER;
	}

	public function getName() : string{
		return "Chest";
	}

	public function getDefaultSize() : int{
		return 27;
	}

	/**
	 * This override is here for documentation and code completion purposes only.
	 * @return Chest
	 */
	public function getHolder(){
		return $this->holder;
	}

	public function onOpen(Player $who) {
		parent::onOpen($who);

		if(count($this->getViewers()) === 1 and ($level = $this->getHolder()->getLevel()) instanceof Level){
			$this->broadcastBlockEventPacket($this->getHolder(), true);
			$level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_CHEST_OPEN);
		}
	}

	public function onClose(Player $who) {
        if((!$this instanceof EnderChestInventory) && $this->getHolder()->getBlock() instanceof TrappedChest) $this->holder->getLevel()->updateAroundRedstone($this->holder, [Vector3::SIDE_DOWN, Vector3::SIDE_NORTH, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, Vector3::SIDE_EAST]);
        if(count($this->getViewers()) === 1 and ($level = $this->getHolder()->getLevel()) instanceof Level){
			$this->broadcastBlockEventPacket($this->getHolder(), false);
			$level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
		}
		parent::onClose($who);
	}

	protected function broadcastBlockEventPacket(Vector3 $vector, bool $isOpen) {
		$pk = new BlockEventPacket();
		$pk->x = (int) $vector->x;
		$pk->y = (int) $vector->y;
		$pk->z = (int) $vector->z;
		$pk->eventType  = 1; //it's always 1 for a chest
		$pk->eventData = +$isOpen;
		$this->getHolder()->getLevel()->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
	}
}
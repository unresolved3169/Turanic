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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\event\player\PlayerGlassBottleEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class GlassBottle extends Item {
	/**
	 * GlassBottle constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::GLASS_BOTTLE, $meta, "Glass Bottle");
	}

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		if($player === null or $player->isSurvival() !== true){
			return false;
		}
		if($blockClicked->getId() === Block::STILL_WATER or $blockClicked->getId() === Block::WATER){
			$player->getServer()->getPluginManager()->callEvent($ev = new PlayerGlassBottleEvent($player, $blockClicked, $this));
			if($ev->isCancelled()){
				return false;
			}else{
				if($this->count <= 1){
					$player->getInventory()->setItemInHand(Item::get(Item::POTION, 0, 1));
					return true;
				}else{
					$this->count--;
					$player->getInventory()->setItemInHand($this);
				}
				if($player->getInventory()->canAddItem(Item::get(Item::POTION, 0, 1)) === true){
					$player->getInventory()->AddItem(Item::get(Item::POTION, 0, 1));
				}else{
					$motion = $player->getDirectionVector()->multiply(0.4);
					$position = clone $player->getPosition();
					$player->getLevel()->dropItem($position->add(0, 0.5, 0), Item::get(Item::POTION, 0, 1), $motion, 40);
				}
				return true;
			}
		}
		return false;
	}
}
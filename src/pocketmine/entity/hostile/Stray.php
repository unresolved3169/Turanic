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

namespace pocketmine\entity\hostile;

use pocketmine\entity\Entity;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;

class Stray extends Skeleton {
	const NETWORK_ID = self::STRAY;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Stray";
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		Entity::spawnTo($player);

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->item = new ItemItem(ItemItem::BOW);
        $pk->inventorySlot = $pk->hotbarSlot = 0;

		$player->dataPacket($pk);
	}

    public function getXpDropAmount(): int{
        return 5;
    }
}

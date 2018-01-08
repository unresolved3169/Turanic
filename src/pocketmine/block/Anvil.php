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

use pocketmine\inventory\AnvilInventory;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class Anvil extends Fallable {

	const NORMAL = 0;
	const SLIGHTLY_DAMAGED = 4;
	const VERY_DAMAGED = 8;

	protected $id = self::ANVIL;

	public function isSolid(){
		return false;
	}

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 5;
	}

	public function getResistance() : float{
		return 6000;
	}

	public function getName() : string{
		$names = [
			self::NORMAL => "Anvil",
			self::SLIGHTLY_DAMAGED => "Slightly Damaged Anvil",
			self::VERY_DAMAGED => "Very Damaged Anvil",
		];
		return $names[$this->meta & 0x0c] ?? "Anvil";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function onActivate(Item $item, Player $player = null){
		if(!$this->getLevel()->getServer()->anvilEnabled){
			return true;
		}
		if($player instanceof Player){
			if($player->isCreative() and $player->getServer()->limitedCreative){
				return true;
			}

			$player->addWindow(new AnvilInventory($this));
			$player->craftingType = Player::CRAFTING_ANVIL;
		}

		return true;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$direction = ($player !== null ? $player->getDirection() : 0) & 0x03;
		$this->meta = ($this->meta & 0x0c) | $direction;
		$bool = $this->getLevel()->setBlock($blockReplace, $this, true, true);
		$player->getLevel()->broadcastLevelEvent($player, LevelEventPacket::EVENT_SOUND_ANVIL_FALL);
		return $bool;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [
                Item::get($this->getItemId(), $this->getDamage() & 0x0c)
			];
		}else{
			return [];
		}
	}

    public function recalculateBoundingBox() : AxisAlignedBB{
        $inset = 0.125;
        if ($this->meta & 0x01) { //east/west
            return new AxisAlignedBB(
                $this->x,
                $this->y,
                $this->z + $inset,
                $this->x + 1,
                $this->y + 1,
                $this->z + 1 - $inset
            );
        } else {
            return new AxisAlignedBB(
                $this->x + $inset,
                $this->y,
                $this->z,
                $this->x + 1 - $inset,
                $this->y + 1,
                $this->z + 1
            );
        }
    }
}

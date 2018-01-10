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

use pocketmine\block\utils\PillarRotationHelper;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Wood extends Solid {
	const OAK = 0;
	const SPRUCE = 1;
	const BIRCH = 2;
	const JUNGLE = 3;

	protected $id = self::WOOD;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getName() : string{
		static $names = [
			self::OAK => "Oak Wood",
			self::SPRUCE => "Spruce Wood",
			self::BIRCH => "Birch Wood",
			self::JUNGLE => "Jungle Wood",
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}

	public function getBurnChance() : int{
		return 5;
	}

	public function getBurnAbility() : int{
		return 10;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        $this->meta = PillarRotationHelper::getMetaFromFace($this->meta, $face);
        return $this->getLevel()->setBlock($blockReplace, $this, true, true);
	}

    public function getVariantBitmask() : int{
        return 0x03;
    }

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function getFuelTime(): int{
        return 300;
    }
}
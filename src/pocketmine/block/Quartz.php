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

use pocketmine\block\utils\PillarRotationHelper;
use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Quartz extends Solid {

	const QUARTZ_NORMAL = 0;
	const QUARTZ_CHISELED = 1;
	const QUARTZ_PILLAR = 2;

    const NORMAL = self::QUARTZ_NORMAL;
    const CHISELED = self::QUARTZ_CHISELED;
    const PILLAR = self::QUARTZ_PILLAR;
    
	protected $id = self::QUARTZ_BLOCK;
	
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	
	public function getHardness() : float{
		return 0.8;
	}
	
	public function getName() : string{
		static $names = [
			self::QUARTZ_NORMAL => "Quartz Block",
            self::QUARTZ_CHISELED => "Chiseled Quartz Block",
			self::QUARTZ_PILLAR => "Quartz Pillar",
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        if($this->meta !== self::NORMAL){
            $this->meta = PillarRotationHelper::getMetaFromFace($this->meta, $face);
        }
        return $this->getLevel()->setBlock($blockReplace, $this, true, true);
	}
	
	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}
	
	public function getToolHarvestLevel(): int{
        return TieredTool::TIER_WOODEN;
    }
    
    public function getVariantBitmask(): int{
        return 0x03;
    }

	public function getDrops(Item $item) : array{
		if($this->isCompatibleWithTool($item)){
			return parent::getDrops($item);
		}else{
			return [];
		}
	}

    public function canHarvestWithHand(): bool{
        return false;
    }
}
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

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneOre extends Solid{

	protected $id = self::REDSTONE_ORE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Redstone Ore";
	}

	public function getHardness() : float{
		return 3;
	}

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        return $this->getLevel()->setBlock($this, $this, true, false);
    }

    public function onUpdate(int $type){
		if($type === Level::BLOCK_UPDATE_NORMAL or $type === Level::BLOCK_UPDATE_TOUCH){
			$this->getLevel()->setBlock($this, BlockFactory::get(Item::GLOWING_REDSTONE_ORE, $this->meta));

			return Level::BLOCK_UPDATE_WEAK;
		}

		return false;
	}

    public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_IRON;
    }

	public function getDropsForCompatibleTool(Item $item): array{
        if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
            return [
                Item::get(Item::REDSTONE_ORE)
            ];
        }else{
            $fortuneL = $item->getEnchantmentLevel(Enchantment::TYPE_MINING_FORTUNE);
            $fortuneL = $fortuneL > 3 ? 3 : $fortuneL;
            return [
                Item::get(Item::REDSTONE_DUST, 0, mt_rand(4, 5 + $fortuneL))
            ];
        }
    }
}

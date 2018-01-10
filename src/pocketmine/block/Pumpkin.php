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

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Pumpkin extends Solid {

	protected $id = self::PUMPKIN;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 1;
	}

	public function isHelmet() : bool{
		return true;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function getName() : string{
		return "Pumpkin";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($player instanceof Player){
			$this->meta = ((int) $player->getDirection() + 1) % 4;
		}
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		if($player != null){
			if($this->checkSnowGolem($player, $blockReplace)) return true;
			$this->checkIronGolem($player,$blockReplace);
		}

		return true;
	}

	public function getVariant(): int{
        return 0;
    }

    public function checkSnowGolem(Player $player, Block $block) : bool{
        if($player->getServer()->allowSnowGolem){
            $level = $this->getLevel();
            $block0 = $level->getBlockAt($block->x, $block->y - 1, $block->z);
            $block1 = $level->getBlockAt($block->x, $block->y - 2, $block->z);
            if(($block0->getId() == $block1->getId()) and $block1->getId() == Item::SNOW_BLOCK){
                $level->setBlockIdAt($block->x, $block->y, $block->z, 0);
                $level->setBlockIdAt($block0->x, $block0->y, $block0->z, 0);
                $level->setBlockIdAt($block1->x, $block1->y, $block1->z, 0);

                $golem = Entity::createEntity("SnowGolem", $player->getLevel(), Entity::createBaseNBT($this));
                if($golem != null) $golem->spawnToAll();
                return true;
            }
        }
        return false;
    }

    public function checkIronGolem(Player $player, Block $block) : bool{
	    // TODO : Add Achievement (Body Guard)
        if($player->getServer()->allowIronGolem){
            $level = $this->getLevel();
            $block0 = $level->getBlock($block->add(0, -1, 0));
            $block1 = $level->getBlock($block->add(0, -2, 0));
            $block2 = $level->getBlock($block->add(-1, -1, 0));
            $block3 = $level->getBlock($block->add(1, -1, 0));
            $block4 = $level->getBlock($block->add(0, -1, -1));
            $block5 = $level->getBlock($block->add(0, -1, 1));
            if($block0->getId() == Item::IRON_BLOCK and $block1->getId() == Item::IRON_BLOCK){
                if($block2->getId() == Item::IRON_BLOCK and $block3->getId() == Item::IRON_BLOCK and $block4->getId() == Item::AIR and $block5->getId() == Item::AIR){
                    $level->setBlockIdAt($block2->x, $block2->y, $block2->z, 0);
                    $level->setBlockIdAt($block3->x, $block3->y, $block3->z, 0);
                }elseif($block4->getId() == Item::IRON_BLOCK and $block5->getId() == Item::IRON_BLOCK and $block2->getId() == Item::AIR and $block3->getId() == Item::AIR){
                    $level->setBlockIdAt($block4->x, $block4->y, $block4->z, 0);
                    $level->setBlockIdAt($block5->x, $block5->y, $block5->z, 0);
                }else return false;
                $level->setBlockIdAt($block->x, $block->y, $block->z, 0);
                $level->setBlockIdAt($block0->x, $block0->y, $block0->z, 0);
                $level->setBlockIdAt($block1->x, $block1->y, $block1->z, 0);

                $golem = Entity::createEntity("IronGolem", $player->getLevel(), Entity::createBaseNBT($this));
                if($golem != null) $golem->spawnToAll();
                return true;
            }
        }
        return false;
    }
}

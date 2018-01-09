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
use pocketmine\block\Fire;
use pocketmine\block\Portal;
use pocketmine\block\Solid;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class FlintSteel extends Tool {
	/** @var Vector3 */
	private $temporalVector = null;

	/**
	 * FlintSteel constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::FLINT_STEEL, $meta, "Flint and Steel");
		if($this->temporalVector === null)
			$this->temporalVector = new Vector3(0, 0, 0);
	}

	// TODO : OPTIMIZE
	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		if($blockClicked->getId() === Block::OBSIDIAN and $player->getServer()->netherEnabled){//黑曜石 4*5最小 23*23最大
			//$level->setBlock($block, new Fire(), true);
			$tx = $blockClicked->getX();
			$ty = $blockClicked->getY();
			$tz = $blockClicked->getZ();
			//x方向
			$x_max = $tx;//x最大值
			$x_min = $tx;//x最小值
			for($x = $tx + 1; $level->getBlock($this->temporalVector->setComponents($x, $ty, $tz))->getId() == Block::OBSIDIAN; $x++){
				$x_max++;
			}
			for($x = $tx - 1; $level->getBlock($this->temporalVector->setComponents($x, $ty, $tz))->getId() == Block::OBSIDIAN; $x--){
				$x_min--;
			}
			$count_x = $x_max - $x_min + 1;//x方向方块
			if($count_x >= 4 and $count_x <= 23){//4 23
				$x_max_y = $ty;//x最大值时的y最大值
				$x_min_y = $ty;//x最小值时的y最大值
				for($y = $ty; $level->getBlock($this->temporalVector->setComponents($x_max, $y, $tz))->getId() == Block::OBSIDIAN; $y++){
					$x_max_y++;
				}
				for($y = $ty; $level->getBlock($this->temporalVector->setComponents($x_min, $y, $tz))->getId() == Block::OBSIDIAN; $y++){
					$x_min_y++;
				}
				$y_max = min($x_max_y, $x_min_y) - 1;//y最大值
				$count_y = $y_max - $ty + 2;//方向方块
				//Server::getInstance()->broadcastMessage("$y_max $x_max_y $x_min_y $x_max $x_min");
				if($count_y >= 5 and $count_y <= 23){//5 23
					$count_up = 0;//上面
					for($ux = $x_min; ($level->getBlock($this->temporalVector->setComponents($ux, $y_max, $tz))->getId() == Block::OBSIDIAN and $ux <= $x_max); $ux++){
						$count_up++;
					}
					//Server::getInstance()->broadcastMessage("$count_up $count_x");
					if($count_up == $count_x){
						for($px = $x_min + 1; $px < $x_max; $px++){
							for($py = $ty + 1; $py < $y_max; $py++){
								$level->setBlock($this->temporalVector->setComponents($px, $py, $tz), new Portal());
							}
						}
                        $this->applyDamage(1);
						return true;
					}
				}
			}

			//z方向
			$z_max = $tz;//z最大值
			$z_min = $tz;//z最小值
			for($z = $tz + 1; $level->getBlock($this->temporalVector->setComponents($tx, $ty, $z))->getId() == Block::OBSIDIAN; $z++){
				$z_max++;
			}
			for($z = $tz - 1; $level->getBlock($this->temporalVector->setComponents($tx, $ty, $z))->getId() == Block::OBSIDIAN; $z--){
				$z_min--;
			}
			$count_z = $z_max - $z_min + 1;
			if($count_z >= 4 and $count_z <= 23){//4 23
				$z_max_y = $ty;//z最大值时的y最大值
				$z_min_y = $ty;//z最小值时的y最大值
				for($y = $ty; $level->getBlock($this->temporalVector->setComponents($tx, $y, $z_max))->getId() == Block::OBSIDIAN; $y++){
					$z_max_y++;
				}
				for($y = $ty; $level->getBlock($this->temporalVector->setComponents($tx, $y, $z_min))->getId() == Block::OBSIDIAN; $y++){
					$z_min_y++;
				}
				$y_max = min($z_max_y, $z_min_y) - 1;//y最大值
				$count_y = $y_max - $ty + 2;//方向方块
				if($count_y >= 5 and $count_y <= 23){//5 23
					$count_up = 0;//上面
					for($uz = $z_min; ($level->getBlock($this->temporalVector->setComponents($tx, $y_max, $uz))->getId() == Block::OBSIDIAN and $uz <= $z_max); $uz++){
						$count_up++;
					}
					//Server::getInstance()->broadcastMessage("$count_up $count_z");
					if($count_up == $count_z){
						for($pz = $z_min + 1; $pz < $z_max; $pz++){
							for($py = $ty + 1; $py < $y_max; $py++){
								$level->setBlock($this->temporalVector->setComponents($tx, $py, $pz), new Portal());
							}
						}
                        $this->applyDamage(1);
						return true;
					}
				}
			}
			//return true;
		}

		if($blockReplace->getId() === self::AIR and ($blockClicked instanceof Solid)){
			$level->setBlock($blockReplace, Block::get(Block::FIRE), true);
			$player->getLevel()->broadcastLevelSoundEvent($blockReplace->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_IGNITE);

			/** @var Fire $block */
			$block = $level->getBlock($blockReplace);
			if($block->getSide(Vector3::SIDE_DOWN)->isTopFacingSurfaceSolid() or $block->canNeighborBurn()){
				$level->scheduleUpdate($block, $block->getTickRate() + mt_rand(0, 10));
				//	return true;
			}

			$this->applyDamage(1);

			return true;
		}

		return false;
	}

	public function getMaxDurability(){
        return 65;
    }
}

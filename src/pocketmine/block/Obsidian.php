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

use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Obsidian extends Solid {

	protected $id = self::OBSIDIAN;

	/** @var Vector3 */
	private $temporalVector = null;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	public function getName() : string{
		return "Obsidian";
	}

    public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_DIAMOND;
    }

    public function getHardness() : float{
        return 35; //50 in PC
    }

    public function getBlastResistance() : float{
        return 6000;
    }

	public function onBreak(Item $item, Player $player = null) : bool{
		parent::onBreak($item, $player);

		if($this->getLevel()->getServer()->netherEnabled){
			for($i = 0; $i <= 6; $i++){
				if($this->getSide($i)->getId() == self::PORTAL){
					break;
				}
				if($i == 6){
					return true;
				}
			}
			$block = $this->getSide($i);
			if($this->getLevel()->getBlock($this->temporalVector->setComponents($block->x - 1, $block->y, $block->z))->getId() == Block::PORTAL or
				$this->getLevel()->getBlock($this->temporalVector->setComponents($block->x + 1, $block->y, $block->z))->getId() == Block::PORTAL
			){//x方向
				for($x = $block->x; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $block->y, $block->z))->getId() == Block::PORTAL; $x++){
					for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
					}
					for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
					}
				}
				for($x = $block->x - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $block->y, $block->z))->getId() == Block::PORTAL; $x--){
					for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
					}
					for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->z))->getId() == Block::PORTAL; $y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->z), new Air());
					}
				}
			}else{//z方向
				for($z = $block->z; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $block->y, $z))->getId() == Block::PORTAL; $z++){
					for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
					}
					for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
					}
				}
				for($z = $block->z - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $block->y, $z))->getId() == Block::PORTAL; $z--){
					for($y = $block->y; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
					}
					for($y = $block->y - 1; $this->getLevel()->getBlock($this->temporalVector->setComponents($block->x, $y, $z))->getId() == Block::PORTAL; $y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->x, $y, $z), new Air());
					}
				}
			}
		}

		return true;
	}
}

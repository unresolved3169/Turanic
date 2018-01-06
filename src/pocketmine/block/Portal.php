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

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Portal extends Transparent {

	protected $id = self::PORTAL;

	/** @var  Vector3 */
	private $temporalVector = null;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Portal";
	}

	public function getHardness() : float{
		return -1;
	}

	public function getResistance() : float{
		return 0;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function canPassThrough() : bool{
		return true;
	}

	public function hasEntityCollision() : bool{
		return true;
	}

	public function onBreak(Item $item){
		$block = $this;
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
		parent::onBreak($item);
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($player instanceof Player){
			$this->meta = $player->getDirection() & 0x01;
		}
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		return true;
	}

	public function getDrops(Item $item) : array{
		return [];
	}

    public function canHarvestWithHand(): bool{
        return false;
    }

    protected function recalculateBoundingBox(){
        return new AxisAlignedBB(
            $this->x,
            $this->y,
            $this->z,
            $this->x + 1,
            $this->y + 1,
            $this->z + 1
        );
    }
}
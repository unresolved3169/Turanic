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
use pocketmine\Player;

class Dirt extends Solid {

	protected $id = self::DIRT;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 0.5;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_SHOVEL;
	}

	public function getName() : string{
		return "Dirt";
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($item->isHoe()){
			$item->useOn($this);
            if($this->meta === 1){
                $this->getLevel()->setBlock($this, Block::get(Block::DIRT), true);
            }else{
                $this->getLevel()->setBlock($this, Block::get(Block::FARMLAND), true);
            }

			return true;
		}

		return false;
	}
}
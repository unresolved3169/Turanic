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

/**
 * Class used for Items that can be Blocks
 */
class ItemBlock extends Item {
    /**
     * ItemBlock constructor.
     *
     * @param int $id
     * @param int $meta
     * @param int $count
     */
	public function __construct(int $id, $meta = 0, int $count = 1){
		$this->block = $block = Block::get($id, $meta & 0xf);
		parent::__construct($block->getId(), $block->getDamage(), $count, $block->getName());
	}

	/**
	 * @param int $meta
	 */
	public function setDamage(int $meta){
		$this->meta = $meta !== -1 ? $meta & 0xf : -1;
		$this->block->setDamage($this->meta !== -1 ? $this->meta : 0);
	}

	public function __clone(){
		$this->block = clone $this->block;
	}

	/**
	 * @return Block
	 */
	public function getBlock() : Block{
		return $this->block;
	}

	public function getFuelTime(): int{
        return $this->block->getFuelTime();
    }

}
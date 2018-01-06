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
use pocketmine\math\Vector3;
use pocketmine\Player;

class EndRod extends Flowable {

	protected $id = self::END_ROD;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel() : int{
		return 14;
	}

	public function getName() : string{
		return "End Rod";
	}

	public function getHardness() : float{
		return 0;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 0,
			1 => 1,
			2 => 3,
			3 => 2,
			4 => 5,
			5 => 4,
		];
		$this->meta = ($blockClicked->getId() === self::END_ROD && $faces[$face] == $blockClicked->getDamage()) ? Vector3::getOppositeSide($faces[$face]) : $faces[$face];
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		return true;
	}

	public function getDrops(Item $item) : array{
		return [
			[$this->id, 0, 1],
		];
	}

}

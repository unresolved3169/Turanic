<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\level\generator\normal\biome;


use pocketmine\block\Block;
use pocketmine\level\generator\normal\populator\Temple;
use pocketmine\level\generator\normal\populator\Well;
use pocketmine\level\generator\populator\Cactus;
use pocketmine\level\generator\populator\DeadBush;
use pocketmine\level\generator\populator\Sugarcane;

class DesertBiome extends SandyBiome {

	/**
	 * DesertBiome constructor.
	 */
	public function __construct(){
		parent::__construct();
        $deadBush = new DeadBush();
        $deadBush->setBaseAmount(1);
        $deadBush->setRandomAmount(4);

        $sugarCane = new Sugarcane();
        $sugarCane->setRandomAmount(20);
        $sugarCane->setBaseAmount(3);

        $sugarCane = new Cactus();
        $sugarCane->setRandomAmount(2);
        $sugarCane->setBaseAmount(3);

        $temple = new Temple();
        $well = new Well();

        $this->addPopulator($well);
        $this->addPopulator($temple);
        $this->addPopulator($deadBush);
        $this->addPopulator($sugarCane);

		$this->setElevation(63, 71);

		$this->temperature = 0.5;
		$this->rainfall = 0;
        $this->setGroundCover([
            Block::get(Block::SAND, 0),
            Block::get(Block::SAND, 0),
            Block::get(Block::SAND, 0),
            Block::get(Block::SAND, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
            Block::get(Block::SANDSTONE, 0),
        ]);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Desert";
	}
}
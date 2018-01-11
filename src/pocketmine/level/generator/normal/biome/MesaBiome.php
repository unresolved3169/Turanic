<?php

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\StainedClay;
use pocketmine\level\generator\populator\Cactus;
use pocketmine\level\generator\populator\DeadBush;

class MesaBiome extends SandyBiome {

	/**
	 * MesaBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$cactus = new Cactus();
		$cactus->setBaseAmount(0);
		$cactus->setRandomAmount(5);
		$deadBush = new DeadBush();
		$cactus->setBaseAmount(2);
		$deadBush->setRandomAmount(10);

		$this->addPopulator($cactus);
		$this->addPopulator($deadBush);

		$this->setElevation(63, 81);

		$this->temperature = 2.0;
		$this->rainfall = 0.8;
		$this->setGroundCover([
			BlockFactory::get(Block::TERRACOTTA, 0),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::PINK),
			BlockFactory::get(Block::TERRACOTTA, 0),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::ORANGE),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::BLACK),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::GRAY),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::WHITE),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::ORANGE),
			BlockFactory::get(Block::TERRACOTTA, 0),
			BlockFactory::get(Block::TERRACOTTA, 0),
			BlockFactory::get(Block::TERRACOTTA, 0),
			BlockFactory::get(Block::TERRACOTTA, 0),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::YELLOW),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::BLACK),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::PINK),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::PINK),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::STAINED_CLAY, StainedClay::WHITE),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
			BlockFactory::get(Block::RED_SANDSTONE, 0),
		]);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Mesa";
	}
} 
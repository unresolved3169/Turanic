<?php

namespace pocketmine\level\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\VariableAmountPopulator;
use pocketmine\utils\Random;

class Igloo extends VariableAmountPopulator{

    /** @var  ChunkManager */
    private $level;

    /*
     * Populate the chunk
     * @param $level pocketmine\level\ChunkManager
     * @param $chunkX int
     * @param $chunkZ int
     * @param $random pocketmine\utils\Random
     */
    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
        $this->level = $level;
        if ($random->nextBoundedInt(100) > 30)
            return;
        $igloo = new \pocketmine\level\generator\normal\object\Igloo();
        $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
        $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
        $y = $this->getHighestWorkableBlock($x, $z) - 1;
        if ($igloo->canPlaceObject($level, $x, $y, $z, $random))
            $igloo->placeObject($level, $x, $y, $z, $random);
    }

    /*
     * Gets the top block (y) on an x and z axes
     * @param $x int
     * @param $z int
     */
    protected function getHighestWorkableBlock($x, $z){
        for ($y = 127; $y > 0; --$y) {
            $b = $this->level->getBlockIdAt($x, $y, $z);
            if ($b === Block::DIRT or $b === Block::GRASS or $b === Block::PODZOL) {
                break;
            } elseif ($b !== 0 and $b !== Block::SNOW_LAYER) {
                return -1;
            }
        }

        return ++$y;
    }

}
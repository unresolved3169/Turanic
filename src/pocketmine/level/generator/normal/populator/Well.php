<?php

namespace pocketmine\level\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\VariableAmountPopulator;
use pocketmine\level\Level;
use pocketmine\utils\Random;

class Well extends VariableAmountPopulator{

    /** @var ChunkManager */
    private $level;

    /**
     * Populates the chunk
     *
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return void
     */
    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
        $this->level = $level;
        if ($random->nextBoundedInt(1000) > 25)
            return; // ~1 chance / 1000 due to building limitations.
        $well = new \pocketmine\level\generator\normal\object\Well();
        $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
        $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
        $y = $this->getHighestWorkableBlock($x, $z) - 1;
        if ($well->canPlaceObject($level, $x, $y, $z, $random))
            $well->placeObject($level, $x, $y, $z, $random);
    }

    protected function getHighestWorkableBlock($x, $z){
        for ($y = Level::Y_MAX - 1; $y > 0; --$y) {
            $b = $this->level->getBlockIdAt($x, $y, $z);
            if ($b === Block::SAND) {
                break;
            }
        }

        return ++$y;
    }

}
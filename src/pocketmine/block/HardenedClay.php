<?php

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class HardenedClay extends Solid {

    protected $id = self::HARDENED_CLAY;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Hardened Clay";
    }

    public function getToolType() : int{
        return Tool::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_WOODEN;
    }

    public function getHardness() : float{
        return 1.25;
    }
}
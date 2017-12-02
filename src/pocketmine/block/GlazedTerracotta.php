<?php

namespace pocketmine\block;

use pocketmine\item\Tool;
use pocketmine\item\Item;
use pocketmine\Player;

class GlazedTerracotta extends Solid{

    public function getHardness() : float{
        return 1.4;
    }
    public function getToolType() : int{
        return Tool::TYPE_PICKAXE;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        if($player !== null){
            $faces = [
                0 => 4,
                1 => 3,
                2 => 5,
                3 => 2
            ];
            $this->meta = $faces[(~($player->getDirection() - 1)) & 0x03];
        }
        return $this->getLevel()->setBlock($block, $this, true, true);
    }

    public function getVariantBitmask() : int{
        return 0;
    }

    public function getDrops(Item $item) : array{
        if($item->isPickaxe() >= Tool::TIER_WOODEN){
            return parent::getDrops($item);
        }
        return [];
    }
}
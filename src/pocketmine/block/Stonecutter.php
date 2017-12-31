<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\item\Item;

class Stonecutter extends Solid{

    protected $id = self::STONECUTTER;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Stonecutter";
    }

    public function getToolType() : int{
        return Tool::TYPE_PICKAXE;
    }

    public function getDrops(Item $item) : array{
        if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
            return parent::getDrops($item);
        }

        return [];
    }
}
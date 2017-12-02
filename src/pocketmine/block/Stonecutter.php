<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

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
        if($item->isPickaxe() >= Tool::TIER_WOODEN){
            return parent::getDrops($item);
        }

        return [];
    }
}
<?php

namespace pocketmine\block;

use pocketmine\item\Item;

class FrostedIce extends Transparent {

    protected $id = self::FROSTED_ICE;

    /**
     * Ice constructor.
     */
    public function __construct($meta = 0){
        $this->meta = $meta;
    }

    public function getHardness(){
        return 0.5;
    }

    public function getName(){
        return "Frosted Ice";
    }

    public function getDrops(Item $item): array{
        return [];
    }
}
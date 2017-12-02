<?php

namespace pocketmine\block;

class UnpoweredComparator extends Solid {

    protected $id = self::UNPOWERED_COMPARATOR_BLOCK;

    public function __construct($meta = 0){ // unfinished
        $this->meta = $meta;
    }

    public function getName(){
        return "Unpowered Comparator";
    }

    public function isActivated(Block $from = null){
        return false;
    }
}
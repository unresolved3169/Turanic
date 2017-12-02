<?php

namespace pocketmine\block;

class PoweredComparator extends Solid{

    protected $id = self::POWERED_COMPARATOR_BLOCK;

    public function __construct($meta = 0){ // unfinished
        $this->meta = $meta;
    }

    public function getName(){
        return "Powered Redstone Comparator";
    }

    public function isActivated(Block $from = null){
        return true;
    }
}
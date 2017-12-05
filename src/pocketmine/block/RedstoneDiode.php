<?php

namespace pocketmine\block;

abstract class RedstoneDiode extends Flowable {

    protected $isPowered = false;

    public function __construct($meta = 0){
        parent::__construct($meta);
    }

    public function isRedstoneSource(){
        return true;
    }

    abstract function getFacing() : int;
}
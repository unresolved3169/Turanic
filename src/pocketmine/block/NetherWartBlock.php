<?php

declare(strict_types=1);

namespace pocketmine\block;

class NetherWartBlock extends Solid{

    protected $id = Block::NETHER_WART_BLOCK;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Nether Wart Block";
    }

    public function getHardness() : float{
        return 1;
    }
}
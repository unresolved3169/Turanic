<?php

namespace pocketmine\block;

use pocketmine\tile\CommandBlock as TileCB;

class ChainCommandBlock extends CommandBlock {

    protected $id = self::CHAIN_COMMAND_BLOCK;

    public function getName(): string{
        return "Chain Command Block";
    }

    public function getBlockType(): int{
        return TileCB::CHAIN;
    }
}
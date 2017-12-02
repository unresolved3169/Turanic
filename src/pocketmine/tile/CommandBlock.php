<?php

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;

class CommandBlock extends Spawnable implements Nameable {

    /** @var string */
    protected $command = "";

    /** @var int */
    protected $successCount = 0;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);

        if(isset($nbt->Command) && $nbt->Command != null){
            $this->command = $nbt->Command->getValue();
        }
    }

    /**
     * @param string $str
     */
    public function setName($str){
        // TODO: Implement setName() method.
    }

    /**
     * @return bool
     */
    public function hasName(){
        return isset($this->namedtag->CustomName);
    }

    public function getName() : string{
        return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "CommandBlock";
    }

    public function getCommand(){
        return $this->command;
    }

    public function runCommand(){
        // TODO
        $this->successCount++;
    }

    public function getSpawnCompound(){
        $nbt = new CompoundTag("", [
            new StringTag("id", Tile::COMMAND_BLOCK),
            new IntTag("x", (int) $this->x),
            new IntTag("y", (int) $this->y),
            new IntTag("z", (int) $this->z),
            new StringTag("Command", $this->getCommand()),
            new IntTag("SuccessCount", $this->successCount)
        ]);
        return $nbt;
    }
}
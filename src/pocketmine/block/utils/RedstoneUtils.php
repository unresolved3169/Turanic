<?php

namespace pocketmine\block\utils;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

abstract class RedstoneUtils{

    protected static $allSides = [
        Vector3::SIDE_DOWN,
        Vector3::SIDE_UP,
        Vector3::SIDE_NORTH,
        Vector3::SIDE_SOUTH,
        Vector3::SIDE_WEST,
        Vector3::SIDE_EAST
    ];

    /**
     * @param Position $position
     * @param int[]    $sides
     */
    public static function updateRedstone(Position $position, array $sides = null){
        if($sides === null) $sides = self::$allSides;
        $level = $position->getLevel();
        foreach($sides as $side){
            $blok = $level->getBlockAt(...$position->getSide($side)->toArray());
            $blok->onUpdate(Level::BLOCK_UPDATE_REDSTONE);
        }
    }

    public static function isRedstonePowered(Position $position) : bool{
        $level = $position->getLevel();
        foreach(self::$allSides as $side){
            $blok = $level->getBlockAt(...$position->getSide($side)->toArray());
            if($blok->getRedstonePower() > 0){
                return true;
            }
        }
        return false;
    }
}
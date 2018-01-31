<?php

namespace pocketmine\block\utils;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

abstract class RedstoneUtils{

    protected static $allSides = [
        Vector3::SIDE_DOWN,
        Vector3::SIDE_UP,
        Vector3::SIDE_NORTH,
        Vector3::SIDE_SOUTH,
        Vector3::SIDE_WEST,
        Vector3::SIDE_EAST
    ];

    protected static $cacheRedstone = [];

    /**
     * @param Position $position
     * @param int[]|null $sides
     * @param bool $cache
     * @param Position $real
     */
    public static function updateRedstone(Position $position, $sides = null, bool $cache = false, Position $real = null){
        if($sides === null) $sides = self::$allSides;
        $level = $position->getLevel();
        $vec = clone $position->asVector3();
        foreach($sides as $side){
            $blok = $level->getBlockAt(...$vec->getSide($side)->toArray());
            if($blok->canUpdateWithRedstone()){
                $blok->onUpdate(Level::BLOCK_UPDATE_REDSTONE);
                if($cache){
                    if($real === null) $real = $position;
                    self::$cacheRedstone[self::positionEncode($blok->asPosition())] = self::positionEncode($real);
                }
            }
        }
    }

    protected static function positionEncode(Position $position) : string{
        return implode(":", $position->toArray());
    }

    protected static function positionDecode(string $encode) : Position{
        $array = explode(":", $encode);
        $array[3] = Server::getInstance()->getLevelByName($array[3]);
        return new Position(...$array);
    }

    public static function isRedstonePowered(Position $position, bool $checkCache = true) : bool{
        if($checkCache){
            $hash = self::positionEncode($position);
            if(!empty(self::$cacheRedstone[$hash])){
                $decode = self::positionDecode(self::$cacheRedstone[$hash]);
                $blok = $decode->getLevel()->getBlockAt(...$decode->asVector3()->toArray());
                if($blok->isRedstoneSource()){
                    return true;
                }else{
                    unset(self::$cacheRedstone[$hash]);
                }
            }
        }

        $level = $position->getLevel();
        foreach(self::$allSides as $side){
            $blok = $level->getBlockAt(...$position->getSide($side)->toArray());
            if($blok->isRedstoneSource() && $blok->getRedstonePower() > 0){
                return true;
            }
        }

        return false;
    }
}
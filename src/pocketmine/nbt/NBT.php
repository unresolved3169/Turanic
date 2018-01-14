<?php

/*
 *
 *    _______                    _
 *   |__   __|                  (_)
 *      | |_   _ _ __ __ _ _ __  _  ___
 *      | | | | | '__/ _` | '_ \| |/ __|
 *      | | |_| | | | (_| | | | | | (__
 *      |_|\__,_|_|  \__,_|_| |_|_|\___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Turanic
 *
 */

declare(strict_types=1);

/**
 * Named Binary Tag handling classes
 */
namespace pocketmine\nbt;

use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\EndTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

abstract class NBT{

    const LITTLE_ENDIAN = 0;
    const BIG_ENDIAN = 1;
    const TAG_End = 0;
    const TAG_Byte = 1;
    const TAG_Short = 2;
    const TAG_Int = 3;
    const TAG_Long = 4;
    const TAG_Float = 5;
    const TAG_Double = 6;
    const TAG_ByteArray = 7;
    const TAG_String = 8;
    const TAG_List = 9;
    const TAG_Compound = 10;
    const TAG_IntArray = 11;

    /**
     * @param int $type
     *
     * @return Tag
     */
    public static function createTag(int $type) : Tag{
        switch($type){
            case self::TAG_End:
                return new EndTag();
            case self::TAG_Byte:
                return new ByteTag();
            case self::TAG_Short:
                return new ShortTag();
            case self::TAG_Int:
                return new IntTag();
            case self::TAG_Long:
                return new LongTag();
            case self::TAG_Float:
                return new FloatTag();
            case self::TAG_Double:
                return new DoubleTag();
            case self::TAG_ByteArray:
                return new ByteArrayTag();
            case self::TAG_String:
                return new StringTag();
            case self::TAG_List:
                return new ListTag();
            case self::TAG_Compound:
                return new CompoundTag();
            case self::TAG_IntArray:
                return new IntArrayTag();
            default:
                throw new \InvalidArgumentException("Unknown NBT tag type $type");
        }
    }

    public static function matchList(ListTag $tag1, ListTag $tag2) : bool{
        if($tag1->getName() !== $tag2->getName() or $tag1->getCount() !== $tag2->getCount()){
            return false;
        }

        foreach($tag1 as $k => $v){
            if(!($v instanceof Tag)){
                continue;
            }

            if(!isset($tag2->{$k}) or !($tag2->{$k} instanceof $v)){
                return false;
            }

            if($v instanceof CompoundTag){
                if(!self::matchTree($v, $tag2->{$k})){
                    return false;
                }
            }elseif($v instanceof ListTag){
                if(!self::matchList($v, $tag2->{$k})){
                    return false;
                }
            }else{
                if($v->getValue() !== $tag2->{$k}->getValue()){
                    return false;
                }
            }
        }

        return true;
    }

    public static function matchTree(CompoundTag $tag1, CompoundTag $tag2) : bool{
        if($tag1->getName() !== $tag2->getName() or $tag1->getCount() !== $tag2->getCount()){
            return false;
        }

        foreach($tag1 as $k => $v){
            if(!($v instanceof Tag)){
                continue;
            }

            if(!isset($tag2->{$k}) or !($tag2->{$k} instanceof $v)){
                return false;
            }

            if($v instanceof CompoundTag){
                if(!self::matchTree($v, $tag2->{$k})){
                    return false;
                }
            }elseif($v instanceof ListTag){
                if(!self::matchList($v, $tag2->{$k})){
                    return false;
                }
            }else{
                if($v->getValue() !== $tag2->{$k}->getValue()){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param CompoundTag $tag1
     * @param CompoundTag $tag2
     * @param bool        $override
     *
     * @return CompoundTag
     */
    public static function combineCompoundTags(CompoundTag $tag1, CompoundTag $tag2, bool $override = false) : CompoundTag{
        $tag1 = clone $tag1;
        foreach($tag2 as $k => $v){
            if(!($v instanceof Tag)){
                continue;
            }
            if(!isset($tag1->{$k}) or (isset($tag1->{$k}) and $override)){
                $tag1->{$k} = clone $v;
            }
        }
        return $tag1;
    }
}
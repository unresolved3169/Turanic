<?php

/*
 *
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
 *
*/

namespace pocketmine\block;

use pocketmine\item\Tool;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\ShulkerBox as TileShulkerBox;

class ShulkerBox extends Transparent {

    protected $id = self::SHULKER_BOX;

    public function __construct($meta = 0){
 		$this->meta = $meta;
 	}

 	public function getToolType(){
        return Tool::TYPE_PICKAXE;
    }

    public function getName(){
        static $names = [
            0 => "White Shulker Box",
            1 => "Orange Shulker Box",
            2 => "Magenta Shulker Box",
            3 => "Light Blue Shulker Box",
            4 => "Yellow Shulker Box",
            5 => "Lime Shulker Box",
            6 => "Pink Shulker Box",
            7 => "Gray Shulker Box",
            8 => "Light Gray Shulker Box",
            9 => "Cyan Shulker Box",
            10 => "Purple Shulker Box",
            11 => "Blue Shulker Box",
            12 => "Brown Shulker Box",
            13 => "Green Shulker Box",
            14 => "Red Shulker Box",
            15 => "Black Shulker Box",
        ];
        return $names[$this->meta & 0x0f];
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        $this->getLevel()->setBlock($this, $this, true, true);
        $nbt = new CompoundTag("", [
            new ListTag("Items", []),
            new StringTag("id", Tile::SHULKER_BOX),
            new IntTag("x", $this->x),
            new IntTag("y", $this->y),
            new IntTag("z", $this->z)
        ]);
        $nbt->Items->setTagType(NBT::TAG_Compound);

        if($item->hasCustomName()){
            $nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
        }

        if($item->hasCustomBlockData()){
            foreach($item->getCustomBlockData() as $key => $v){
                $nbt->{$key} = $v;
            }
        }

        Tile::createTile(Tile::SHULKER_BOX, $this->getLevel(), $nbt);
    }

    public function onActivate(Item $item, Player $player = null){
        if($player instanceof Player){
            $top = $this->getSide(1);
            if($top->isTransparent() !== true){
                return true;
            }

            $t = $this->getLevel()->getTile($this);
            $sb = null;
            if($t instanceof TileShulkerBox){
                $sb = $t;
            }else{
                $nbt = new CompoundTag("", [
                    new ListTag("Items", []),
                    new StringTag("id", Tile::CHEST),
                    new IntTag("x", $this->x),
                    new IntTag("y", $this->y),
                    new IntTag("z", $this->z)
                ]);
                $nbt->Items->setTagType(NBT::TAG_Compound);
                $sb = Tile::createTile("Chest", $this->getLevel(), $nbt);
            }

            if(isset($sb->namedtag->Lock) and $sb->namedtag->Lock instanceof StringTag){
                if($sb->namedtag->Lock->getValue() !== $item->getCustomName()){
                    return true;
                }
            }

            if($player->isCreative() and $player->getServer()->limitedCreative){
                return true;
            }
            $player->addWindow($sb->getInventory());
        }

        return true;
    }

    public function getHardness(){
        return 6;
    }

    public function canBeActivated(): bool{
        return true;
    }

    public function getDrops(Item $item): array{
        return [
            [$this->id, 0, 1]
        ];
    }

}
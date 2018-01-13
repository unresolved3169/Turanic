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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Container;
use pocketmine\tile\Tile;
use pocketmine\tile\ShulkerBox as TileShulkerBox;

class ShulkerBox extends Transparent {

    protected $id = self::SHULKER_BOX;

    public function __construct(int $meta = 0){
 		$this->meta = $meta;
 	}

 	public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getName(){
        return ColorBlockMetaHelper::getColorFromMeta($this->getVariant()) . " Shulker Box";
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        $this->getLevel()->setBlock($this, $this, true, true);
        Tile::createTile(Tile::SHULKER_BOX, $this->getLevel(), TileShulkerBox::createNBT($this, $face, $item, $player));
        return true;
    }

    public function onActivate(Item $item, Player $player = null) : bool{
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
                $sb = Tile::createTile(Tile::SHULKER_BOX, $this->getLevel(), TileShulkerBox::createNBT($this));
            }

            if(!($this->getSide(Vector3::SIDE_UP)->isTransparent()) or ($sb->namedtag->hasTag("Lock", StringTag::class) and $sb->namedtag->getString("Lock") !== $item->getCustomName())) {
                return true;
            }

            $player->addWindow($sb->getInventory());
        }

        return true;
    }

    public function onBreak(Item $item, Player $player = null) : bool{
        $t = $this->getLevel()->getTile($this);
        if ($t instanceof TileShulkerBox) {
            $item = Item::get(Item::SHULKER_BOX, $this->meta, 1);
            $itemNBT = clone $item->getNamedTag();
            $itemNBT->setTag($t->getNBT()->getTag(Container::TAG_ITEMS));
            $item->setNamedTag($itemNBT);
            $this->getLevel()->dropItem($this->asVector3(), $item);
            $t->getInventory()->clearAll(); // dont drop the items
        }
        $this->getLevel()->setBlock($this, BlockFactory::get(Block::AIR), true, true);

        return true;
    }

    public function getHardness() : float{
        return 6;
    }

    public function getDropsForCompatibleTool(Item $item): array{
        return [];
    }

    public function getVariantBitmask(): int{
        return 0x0f;
    }

}
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

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneWire extends Flowable {

    const ON = 1;
    const OFF = 2;
    const PLACE = 3;
    const DESTROY = 4;

    protected $id = self::REDSTONE_WIRE;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Redstone Wire";
    }

    public function getStrength(){
        return $this->meta;
    }

    public function isActivated(Block $from = null){
        return ($this->meta > 0);
    }

    public function onUpdate($type){
        switch($type){
            case Level::BLOCK_UPDATE_NORMAL:
                if($this->cantBePlacedOn()){
                    $this->getLevel()->useBreakOn($this);
                    return Level::BLOCK_UPDATE_NORMAL;
                }
                break;
            case Level::BLOCK_UPDATE_REDSTONE:
                $kontrol = false;
                if(!$this->isActivated()){
                    foreach ([self::SIDE_NORTH, self::SIDE_SOUTH, self::SIDE_WEST, self::SIDE_EAST] as $side) {
                        /** @var RedstoneWire $wire */
                        $wire = $this->getSide($side);
                        if($wire->getId() == $this->id){
                            if($wire->isActivated()){
                                $kontrol = true; // check connect
                                break;
                            }
                        }
                    }
                }
                if(!$kontrol)
                    $this->calcSignal($this->isActivated() ? 0 : 15, $this->isActivated() ? self::OFF : self::ON);
                break;
        }
        return true;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        if(!$this->cantBePlacedOn()){
            $this->getLevel()->setBlock($blockReplace, $this, true, false);
            $this->calcSignal(15, self::PLACE);
        }
        return true;
    }

    public function updateNormalWire(Block $block, $strength, $type, array $hasUpdated){
        /** @var RedstoneWire $block */
        if($block->getId() == Block::REDSTONE_WIRE){
            if($block->getStrength() < $strength){
                return $block->calcSignal($strength, $type, $hasUpdated);
            }
        }
        return $hasUpdated;
    }

    public function getPowerSources(RedstoneWire $wire, array $powers = [], array $hasUpdated = [], $isStart = false){
        if(!$isStart){
            $wire->meta = 0;
            $wire->getLevel()->setBlock($wire, $wire, true, false);
            $wire->updateAround();
        }
        $hasChecked = [
            self::SIDE_WEST => false,
            self::SIDE_EAST => false,
            self::SIDE_NORTH => false,
            self::SIDE_SOUTH => false
        ];
        $hash = Level::blockHash($wire->x, $wire->y, $wire->z);
        if(!isset($hasUpdated[$hash])) $hasUpdated[$hash] = true;
        else return [$powers, $hasUpdated];
        //check blocks around
        foreach($hasChecked as $side => $bool){
            /** @var RedstoneWire $block */
            $block = $wire->getSide($side);
            if($block->isRedstoneSource()){
                if($block->isActivated($wire)){
                    if($block->getId() != $this->id){
                        $powers[] = $block;
                    }else{
                        $result = $this->getPowerSources($block, $powers, $hasUpdated);
                        $powers = $result[0];
                        $hasUpdated = $result[1];
                    }
                    $hasChecked[$side] = true;
                }
            }
        }
        //check blocks above
        $baseBlock = $wire->add(0, 1, 0);
        foreach($hasChecked as $side => $bool){
            if(!$bool){
                $block = $this->getLevel()->getBlock($baseBlock->getSide($side));
                if($block->isRedstoneSource()){
                    if($block->isActivated($wire)){
                        if($block->getId() == $this->id){
                            $result = $this->getPowerSources($block, $powers, $hasUpdated);
                            $powers = $result[0];
                            $hasUpdated = $result[1];
                            $hasChecked[$side] = true;
                        }
                    }
                }
            }
        }
        //check blocks below
        $baseBlock = $wire->add(0, -1, 0);
        foreach($hasChecked as $side => $bool){
            if(!$bool){
                $block = $this->getLevel()->getBlock($baseBlock->getSide($side));
                if($block->isRedstoneSource()){
                    if($block->isActivated($wire)){
                        if($block->getId() == $this->id){
                            $result = $this->getPowerSources($block, $powers, $hasUpdated);
                            $powers = $result[0];
                            $hasUpdated = $result[1];
                            $hasChecked[$side] = true;
                        }
                    }
                }
            }
        }
        return [$powers, $hasUpdated];
    }

    public function getHighestStrengthAround(){
        $strength = 0;
        $hasChecked = [
            self::SIDE_WEST => false,
            self::SIDE_EAST => false,
            self::SIDE_NORTH => false,
            self::SIDE_SOUTH => false
        ];
        //check blocks around
        foreach($hasChecked as $side => $bool){
            $block = $this->getSide($side);
            if($block->isRedstoneSource()){
                if(($block->getWeakPower($side) > $strength) and $block->isActivated($this)) $strength = $block->getWeakPower($side);
                $hasChecked[$side] = true;
            }
        }
        //check blocks above
        $baseBlock = $this->add(0, 1, 0);
        foreach($hasChecked as $side => $bool){
            if(!$bool){
                $block = $this->getLevel()->getBlock($baseBlock->getSide($side));
                if($block->getId() == $this->id){
                    if($block->getWeakPower($side) > $strength) $strength = $block->getWeakPower($side);
                    $hasChecked[$side] = true;
                }
            }
        }
        //check blocks below
        $baseBlock = $this->add(0, -1, 0);
        foreach($hasChecked as $side => $bool){
            if(!$bool){
                $block = $this->getLevel()->getBlock($baseBlock->getSide($side));
                if($block->getId() == $this->id){
                    if($block->getWeakPower($side) > $strength) $strength = $block->getWeakPower($side);
                    $hasChecked[$side] = true;
                }
            }
        }
        unset($block);
        unset($hasChecked);
        return $strength;
    }

    public function updateAround(){
        $side = $this->getUnconnectedSide();
        $sides = [self::SIDE_WEST, self::SIDE_EAST, self::SIDE_SOUTH, self::SIDE_NORTH];
        $this->level->updateAroundRedstone($this->getSide(self::SIDE_DOWN), $sides);
        $this->getSide($side[0])->onUpdate(Level::BLOCK_UPDATE_REDSTONE);
        if($side[0] != false) {
            $block = $this->getSide($side[0]);
            if(!$block->isTransparent()){
                $sides = [self::SIDE_WEST, self::SIDE_EAST, self::SIDE_SOUTH, self::SIDE_NORTH, self::SIDE_UP, self::SIDE_DOWN];
                unset($sides[array_search(static::getOppositeSide($side[0]), $sides)]);
                $this->level->updateAroundRedstone($this, $sides);
            }
        }
    }

    public function calcSignal($strength = 15, $type = self::ON, array $hasUpdated = []){
        $hash = Level::blockHash($this->x, $this->y, $this->z);
        if(!in_array($hash, $hasUpdated)){
            $hasUpdated[] = $hash;
            if($type == self::DESTROY or $type == self::OFF){
                $this->meta = $strength;
                $this->getLevel()->setBlock($this, $this, true, false);
                if($type == self::DESTROY) $this->getLevel()->setBlock($this, new Air(), true, false);
                if($strength <= 0) $this->updateAround();
                $powers = $this->getPowerSources($this, [], [], true);
                /** @var Block $power */
                foreach($powers[0] as $power) {
                    foreach ([self::SIDE_DOWN, self::SIDE_UP, self::SIDE_NORTH, self::SIDE_SOUTH, self::SIDE_WEST, self::SIDE_EAST] as $side) {
                        /** @var RedstoneWire $wire */
                        $wire = $power->getSide($side);
                        if($wire->getId() == $this->id){
                            $wire->calcSignal(15, self::ON);
                        }
                    }
                }
            }else{
                if($strength <= 0) return $hasUpdated;
                if($type == self::PLACE) $strength = $this->getHighestStrengthAround() - 1;
                if($type == self::ON) $type = self::PLACE;
                if($this->getStrength() < $strength){
                    $this->meta = $strength;
                    $this->getLevel()->setBlock($this, $this, true, false);
                    $this->updateAround();
                    $hasChecked = [
                        self::SIDE_WEST => false,
                        self::SIDE_EAST => false,
                        self::SIDE_NORTH => false,
                        self::SIDE_SOUTH => false
                    ];
                    foreach($hasChecked as $side => $bool){
                        $needUpdate = $this->getSide($side);
                        if(!in_array(Level::blockHash($needUpdate->x, $needUpdate->y, $needUpdate->z), $hasUpdated)){
                            $result = $this->updateNormalWire($needUpdate, $strength - 1, $type, $hasUpdated);
                            if(count($result) != count($hasUpdated)){
                                $hasUpdated = $result;
                                $hasChecked[$side] = true;
                            }
                        }
                    }
                    $baseBlock = $this->add(0, 1, 0);
                    foreach($hasChecked as $side => $bool){
                        if(!$bool){
                            $needUpdate = $this->getLevel()->getBlock($baseBlock->getSide($side));
                            if(!in_array(Level::blockHash($needUpdate->x, $needUpdate->y, $needUpdate->z), $hasUpdated)){
                                $result = $this->updateNormalWire($needUpdate, $strength - 1, $type, $hasUpdated);
                                if(count($result) != count($hasUpdated)){
                                    $hasUpdated = $result;
                                    $hasChecked[$side] = true;
                                }
                            }
                        }
                    }
                    $baseBlock = $this->add(0, -1, 0);
                    foreach($hasChecked as $side => $bool){
                        if(!$bool){
                            $needUpdate = $this->getLevel()->getBlock($baseBlock->getSide($side));
                            if(!in_array(Level::blockHash($needUpdate->x, $needUpdate->y, $needUpdate->z), $hasUpdated)){
                                $result = $this->updateNormalWire($needUpdate, $strength - 1, $type, $hasUpdated);
                                if(count($result) != count($hasUpdated)){
                                    $hasUpdated = $result;
                                    $hasChecked[$side] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $hasUpdated;
    }

    /**
     * @param Item $item
     *
     * @return mixed|void
     */
    public function onBreak(Item $item){
        $this->calcSignal(0, self::DESTROY);
    }

    /**
     * @param Item $item
     *
     * @return array
     */
    public function getDrops(Item $item) : array{
        return [
            [Item::REDSTONE, 0, 1]
        ];
    }

    private function cantBePlacedOn($side = self::SIDE_DOWN){
        $down = $this->getSide($side);
        return $down instanceof Transparent and $down->getId() != Block::REDSTONE_LAMP and $down->getId() != Block::LIT_REDSTONE_LAMP;
    }

    public function isRedstoneSource(){
        return ($this->meta > 0);
    }

    public function getUnconnectedSide(){
        $connected = [];
        $notConnected = [];
        foreach($this->getConnectedWires() as $key => $bool){
            if($bool){
                $connected[] = $key;
            }else $notConnected[] = $key;
        }
        if(count($connected) == 1){
            return [static::getOppositeSide($connected[0]), $connected];
        }elseif(count($connected) == 3){
            return [$notConnected[0], $connected];
        }else return [false, $connected];
    }

    public function getConnectedWires(){
        $hasChecked = [
            self::SIDE_WEST => false,
            self::SIDE_EAST => false,
            self::SIDE_NORTH => false,
            self::SIDE_SOUTH => false
        ];
        //check blocks around
        foreach($hasChecked as $side => $bool){
            $block = $this->getSide($side);
            if($block->isRedstoneSource() and !$block instanceof PoweredRepeater){
                $hasChecked[$side] = true;
            }
            if($block instanceof PoweredRepeater){
                if($this->equals($block->getSide($block->getOppositeDirection()))){
                    $hasChecked[$side] = true;
                }
            }
        }
        //check blocks above
        $baseBlock = $this->add(0, 1, 0);
        foreach($hasChecked as $side => $bool){
            if(!$bool){
                $block = $this->getLevel()->getBlock($baseBlock->getSide($side));
                if($block->getId() == $this->id){
                    $hasChecked[$side] = true;
                }
            }
        }
        //check blocks below
        $baseBlock = $this->add(0, -1, 0);
        foreach($hasChecked as $side => $bool){
            if(!$bool){
                $block = $this->getLevel()->getBlock($baseBlock->getSide($side));
                if($block->getId() == $this->id){
                    $hasChecked[$side] = true;
                }
            }
        }
        unset($block);
        return $hasChecked;
    }

    public function getWeakPower(int $side): int{
        return $this->getStrength();
    }
}

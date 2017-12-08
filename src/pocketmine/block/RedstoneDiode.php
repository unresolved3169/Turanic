<?php

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

abstract class RedstoneDiode extends Flowable {

    protected $isPowered = false;

    public function __construct($meta = 0){
        parent::__construct($meta);
    }

    public function isRedstoneSource(){
        return true;
    }

    abstract function getFacing() : int;

    public function onBreak(Item $item){
        $this->level->setBlock($this, new Air(), true, true);
        $sides = [self::SIDE_WEST, self::SIDE_EAST, self::SIDE_SOUTH, self::SIDE_NORTH, self::SIDE_UP, self::SIDE_DOWN];
        foreach($sides as $side){
            $this->level->updateAroundRedstone($this->getSide($side));
        }
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        if($this->getSide(self::SIDE_DOWN)->isTransparent()){
            return false;
        }

        if($player instanceof Player){
            $this->meta = ((int) $player->getDirection() + 5) % 4;
        }
        $this->getLevel()->setBlock($block, $this, true, true);
        if($this->shouldBePowered()){
            $this->level->scheduleUpdate($this, 1);
        }

        return true;
    }

    public function onUpdate($type){
        switch ($type) {
            case Level::BLOCK_UPDATE_SCHEDULED:
                if (!$this->isLocked()) {
                    $shouldBePowered = $this->shouldBePowered();

                    if ($this->isPowered && !$shouldBePowered) {
                        $this->level->setBlock($this, $this->getUnpowered(), true, true);

                        $this->level->updateAroundRedstone($this->getSide(static::getOppositeSide($this->getFacing())));
                    } else if (!$this->isPowered) {
                        $this->level->setBlock($this, $this->getPowered(), true, true);
                        $this->level->updateAroundRedstone($this->getSide(static::getOppositeSide($this->getFacing())));

                        if (!$shouldBePowered) {
                            $this->level->scheduleUpdate($this->getPowered(), $this->getDelay());
                        }
                    }
                }
                break;
            case Level::BLOCK_UPDATE_NORMAL:
            case Level::BLOCK_UPDATE_REDSTONE:
                if ($type == Level::BLOCK_UPDATE_NORMAL && $this->getSide(self::SIDE_DOWN)->isTransparent()) {
                    $this->level->useBreakOn($this);
                    return Level::BLOCK_UPDATE_NORMAL;
                } else {
                    $this->updateState();
                    return Level::BLOCK_UPDATE_NORMAL;
                }
                break;
        }
        return 0;
    }

    public function updateState(){
        if (!$this->isLocked()) {
            $shouldPowered = $this->shouldBePowered();

            if (($this->isPowered && !$shouldPowered || !$this->isPowered && $shouldPowered) && !$this->level->isUpdateScheduled($this, $this)) {
                $this->level->scheduleUpdate($this, $this->getDelay());
            }
        }
    }

    public function isLocked():bool{
        return false;
    }

    protected function calculateInputStrength():int{
        $face = $this->getFacing();
        $pos = $this->getSide($face);
        $power = $this->level->getRedstonePower($pos, $face);

        if($power >= 15)
            return $power;
        else
            return max($power, $pos->getId() == Block::REDSTONE_WIRE ? $pos->meta : 0);
    }

    protected function getPowerOnSides(Vector3 $pos = null, int $side = null) : int{
        if($pos == null or $side == null){
            $face = $this->getFacing();
            $face1 = Vector3::rotateY($face);
            $face2 = Vector3::rotateYCCW($face);
            return max($this->getPowerOnSides($this->getSide($face1), $face1), $this->getPowerOnSides($this->getSide($face2), $face2));
        }

        $block = $this->level->getBlock($pos);
        return $this->isAlternateInput($block) ? ($block->getId() == Block::REDSTONE_BLOCK ? 15 : ($block->getId() == Block::REDSTONE_WIRE ? $block->meta : $this->getWeakPower($side))) : 0;
    }

    protected function shouldBePowered() : bool{
        return $this->calculateInputStrength() > 0;
    }

    protected abstract function getDelay() : int;
    protected abstract function getUnpowered() : Block;
    protected abstract function getPowered() : Block;

    public function recalculateBoundingBox(){
        return new AxisAlignedBB($this->x, $this->y, $this->z, $this->x + 1, $this->y + 0.125, $this->z + 1);
    }

    public function canPassThrough(){
        return false;
    }

    protected function getRedstoneSignal() : int{
        return 15;
    }

    protected function isAlternateInput(Block $block) : bool{
        return $block->isRedstoneSource();
    }

    public function getWeakPower(int $side): int{
        return !$this->isPowered() ? 0 : ($this->getFacing() == $side ? $this->getRedstoneSignal() : 0);
    }

    public function canBeActivated(): bool{
        return true;
    }

    /**
     * @return bool
     */
    public function isPowered(): bool{
        return $this->isPowered;
    }

    public function isFacingTowardsRepeater():bool{
        $side = static::getOppositeSide($this->getFacing());
        $block = $this->getSide($side);
        return $block instanceof RedstoneDiode && $block->getFacing() != $side;
    }
}
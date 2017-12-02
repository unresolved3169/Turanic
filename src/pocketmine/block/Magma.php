<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class Magma extends Solid{

    protected $id = Block::MAGMA;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Magma Block";
    }

    public function getHardness() : float{
        return 0.5;
    }

    public function getToolType() : int{
        return Tool::TYPE_PICKAXE;
    }

    public function getLightLevel() : int{
        return 3;
    }

    public function hasEntityCollision() : bool{
        return true;
    }

    public function onEntityCollide(Entity $entity) : void{
        if(!$entity->isSneaking()){
            $ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_FIRE, 1);
            $entity->attack($ev);
        }
    }

    public function getDrops(Item $item) : array{
        if($item->isPickaxe() >= Tool::TIER_WOODEN){
            return parent::getDrops($item);
        }

        return [];
    }

}
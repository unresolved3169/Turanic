<?php

namespace pocketmine\block;

use pocketmine\entity\Living;

class StonePressurePlate extends PressurePlate {
	protected $id = self::STONE_PRESSURE_PLATE;

	public function __construct($meta = 0){
        parent::__construct($meta);
        $this->onPitch = 0.6;
        $this->offPitch = 0.5;
    }

    /**
	 * @return string
	 */
	public function getName() : string{
		return "Stone Pressure Plate";
	}

    protected function computeRedstoneStrength(): int{
        $bbs = $this->getCollisionBoxes();

        foreach($bbs as $bb){
            foreach($this->level->getCollidingEntities($bb) as $entity){
                if($entity instanceof Living && $entity->doesTriggerPressurePlate()){
                    return 15;
                }
            }
        }
        return 0;
    }
}
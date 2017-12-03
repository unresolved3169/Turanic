<?php

namespace pocketmine\entity\passive;

use pocketmine\entity\Animal;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Villager extends Animal {
	
	const NETWORK_ID = 15;

    const PROFESSION_FARMER = 0;
    const PROFESSION_LIBRARIAN = 1;
    const PROFESSION_PRIEST = 2;
    const PROFESSION_BLACKSMITH = 3;
    const PROFESSION_BUTCHER = 4;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

    public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));

		parent::initEntity();

        /** @var int $profession */
        $profession = $this->namedtag["Profession"] ?? self::PROFESSION_FARMER;
        if($profession > 4 or $profession < 0){
            $profession = self::PROFESSION_FARMER;
        }
        $this->setProfession($profession);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Villager";
	}
	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
        $pk->entityRuntimeId = $this->getId();
		$pk->type = Villager::NETWORK_ID;
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

    /**
     * Sets the villager profession
     *
     * @param int $profession
     */
    public function setProfession(int $profession){
        $this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $profession);
    }

    public function getProfession() : int{
        return $this->getDataProperty(self::DATA_VARIANT);
    }
}

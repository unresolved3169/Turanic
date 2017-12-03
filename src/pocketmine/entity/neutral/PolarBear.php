<?php

namespace pocketmine\entity\neutral;

use pocketmine\entity\Monster;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};


class PolarBear extends Monster {
	const NETWORK_ID = 28;

	public $width = 0.6;
	public $length = 0.9;
	public $height = 0;

	public $dropExp = [5, 5];
	
	public $drag = 0.2;
	public $gravity = 0.3;


	/**
	 * @return string
	 */
	public function getName(){
		return "Polar Bear";
	}

	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		
		$this->setMaxHealth(30);
		parent::initEntity();
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = PolarBear::NETWORK_ID;
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$drops = [ItemItem::get(ItemItem::RAW_SALMON, 0, mt_rand(0, 2))];
		$drops[] = ItemItem::get(ItemItem::RAW_FISH, 0, mt_rand(0, 2));
		return $drops;
	}
}
<?php

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Minecart extends Item {
	/**
	 * Minecart constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::MINECART, $meta, $count, "Minecart");
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

    /**
     * @return int
     */
    public function getMaxStackSize(): int{
	    return 1;
    }

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		$minecart = Entity::createEntity("Minecart", $player->getLevel(), Entity::createBaseNBT($blockReplace->add(0,0.8,0)));
		if($minecart != null) $minecart->spawnToAll();

		if($player->isSurvival()){
			$item = $player->getInventory()->getItemInHand();
			$count = $item->getCount();
			if(--$count <= 0){
				$player->getInventory()->setItemInHand(Item::get(Item::AIR));
				return true;
			}

			$item->setCount($count);
			$player->getInventory()->setItemInHand($item);
		}

		return true;
	}
}

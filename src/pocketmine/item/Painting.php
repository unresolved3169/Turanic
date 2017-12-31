<?php

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

// TODO : OPTIMIZE
class Painting extends Item {
	/**
	 * Painting constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::PAINTING, 0, $count, "Painting");
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		if($blockClicked->isTransparent() === false and $face > 1 and $blockReplace->isSolid() === false){
			$faces = [
				2 => 1,
				3 => 3,
				4 => 0,
				5 => 2,
			];
			$motives = [
				// Motive Width Height
				["Kebab", 1, 1],
				["Aztec", 1, 1],
				["Alban", 1, 1],
				["Aztec2", 1, 1],
				["Bomb", 1, 1],
				["Plant", 1, 1],
				["Wasteland", 1, 1],
				["Wanderer", 1, 2],
				["Graham", 1, 2],
				["Pool", 2, 1],
				["Courbet", 2, 1],
				["Sunset", 2, 1],
				["Sea", 2, 1],
				["Creebet", 2, 1],
				["Match", 2, 2],
				["Bust", 2, 2],
				["Stage", 2, 2],
				["Void", 2, 2],
				["SkullAndRoses", 2, 2],
				//array("Wither", 2, 2),
				["Fighters", 4, 2],
				["Skeleton", 4, 3],
				["DonkeyKong", 4, 3],
				["Pointer", 4, 4],
				["Pigscene", 4, 4],
				["Flaming Skull", 4, 4],
			];

			$right = [4, 5, 3, 2];

			$validMotives = [];
			foreach($motives as $motive){
				$valid = true;
				for($x = 0; $x < $motive[1] && $valid; $x++){
					for($z = 0; $z < $motive[2] && $valid; $z++){
						if($blockClicked->getSide($right[$face - 2], $x)->isTransparent() ||
							$blockClicked->getSide(Vector3::SIDE_UP, $z)->isTransparent() ||
                            $blockReplace->getSide($right[$face - 2], $x)->isSolid() ||
                            $blockReplace->getSide(Vector3::SIDE_UP, $z)->isSolid()
						){
							$valid = false;
						}
					}
				}

				if($valid){
					$validMotives[] = $motive;
				}
			}

			$motive = $motives[mt_rand(0, count($validMotives) - 1)];

            $nbt = Entity::createBaseNBT($blockClicked, null, $faces[$face] * 90);
            $nbt->setString("Motive", $motive[0]);

			$painting = Entity::createEntity("Painting", $player->getLevel(), $nbt);
			if($painting != null) $painting->spawnToAll();

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

		return false;
	}

}
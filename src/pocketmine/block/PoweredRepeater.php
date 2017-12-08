<?php

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\Player;

class PoweredRepeater extends RedstoneDiode {
	protected $id = self::POWERED_REPEATER_BLOCK;

	/**
	 * PoweredRepeater constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
		$this->isPowered = true;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Powered Repeater";
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
	public function getStrength(){
		return 15;
	}

	/**
	 * @return int
	 */
	public function getFacing() : int{
		$direction = 0;
		switch($this->meta % 4){
			case 0:
				$direction = 3;
				break;
			case 1:
				$direction = 4;
				break;
			case 2:
				$direction = 2;
				break;
			case 3:
				$direction = 5;
				break;
		}
		return $direction;
	}

	/**
	 * @return int
	 */
	public function getOppositeDirection() : int{
		return static::getOppositeSide($this->getFacing());
	}

	protected function isAlternateInput(Block $block) : bool {
        return $block instanceof RedstoneDiode;
	}

    /**
	 * @return int
	 */
	public function getDelay() : int{
		return (1 + ($this->meta >> 2)) * 2;
	}

	protected  function getPowered(): Block{
        return $this;
    }

    protected  function getUnpowered(): Block{
        return new UnpoweredRepeater($this->meta);
    }

    public function getLightLevel(){
        return 7;
    }

	public function onActivate(Item $item, Player $player = null){
        $this->meta += 4;
        if($this->meta > 15) $this->meta = $this->meta % 4;

        $this->level->setBlock($this, $this, true, false);
        return true;
    }

    public function isLocked(): bool{
        return $this->getPowerOnSides() > 0;
    }
}

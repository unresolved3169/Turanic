<?php

namespace pocketmine\block;

class UnpoweredRepeater extends PoweredRepeater {
	protected $id = self::UNPOWERED_REPEATER_BLOCK;

	public function __construct($meta = 0){
        parent::__construct($meta);
        $this->isPowered = false;
    }

    /**
	 * @return string
	 */
	public function getName() : string{
		return "Unpowered Repeater";
	}

	protected function getUnpowered(): Block{
        return $this;
    }

    protected function getPowered(): Block{
        return new PoweredRepeater($this->meta);
    }

    /**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return false;
	}

	public function getLightLevel(){
        return 0;
    }
}
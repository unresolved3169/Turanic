<?php

namespace pocketmine\block;

class RedstoneSource extends Flowable {

    /**
     * RedstoneSource constructor.
     * @param int $meta
     */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}
}
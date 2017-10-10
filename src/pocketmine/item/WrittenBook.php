<?php

/*
 *
 *    _______                    _
 *   |__   __|                  (_)
 *      | |_   _ _ __ __ _ _ __  _  ___
 *      | | | | | '__/ _` | '_ \| |/ __|
 *      | | |_| | | | (_| | | | | | (__
 *      |_|\__,_|_|  \__,_|_| |_|_|\___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Turanic
 *
 */

namespace pocketmine\item;

use pocketmine\nbt\tag\{ListTag, NamedTag, StringTag};

class WrittenBook extends BookAndQuill{
	/**
	 * WrittenBook constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WRITTEN_BOOK, 0, $count, "WrittenBook");
	}
	
	public function setAuthor(string $name){
		return $this->setOption("author", new StringTag("author", $name));
	}
	
	public function getAuthor() : string{
		return $this->getOption("author");
	}
	
	public function setTitle(string $name){
		return $this->setOption("title", new StringTag("title", $name));
	}
	
	public function getTitle() : string{
		return $this->getOption("title");
	}
}
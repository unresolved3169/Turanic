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
use pocketmine\network\protocol\mcpe\BookEditPacket;
use pocketmine\entity\Entity;
use pocketmine\Player;

class BookAndQuill extends Item {
	/**
	 * BookAndQuill constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::BOOK_AND_QUILL, 0, $count, "BookAndQuill");
	}
	
	public function setOption(string $name, $value){
		$nbt = $this->getNamedTag();
		$nbt->{$name} = $value;
		$this->setNamedTag($nbt);
	}
	
	public function getOption(string $name){
		$v = $this->getNamedTag()->{$name} ?? "";
		if($v instanceof NamedTag) $v = $v->getValue();
		
		return $v;
	}
	
	public function getPages() : array{
		return $this->getOption("pages");
	}
	
	public function setPage(int $pageNumber, string $text, string $photoName = ""){
		$nbt = $this->getNamedTag();
		if(!isset($nbt->pages)){
			$nbt->pages = new ListTag("pages", []);
		}
		$nbt->pages->{$pageNumber} = new StringTag("$pageNumber", json_encode(["photoname" => $photoname, "text" => $text]));
		
		$this->setNamedTag($nbt);
	}
	
	public function getPage(int $pageNumber) : array{
		/** 
		 * array contains: pageTitle and pageText fields
		 */
		$pages = $this->getPages();
		if(isset($pages[$pageNumber])){
			$page = $pages[$pageNumber];
			return json_decode($page->getValue());
		}
		return ["photoname" => "null", "text" => "null"]; //empty
	}
}
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

namespace pocketmine;

use pocketmine\lang\BaseLang;

class Translate{
	
	const ENG = "eng";
	const TUR = "tur";
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
    /*public static function checkTurkish(){
    	$server = Server::getInstance();
    
    	$isTurkish = "no";
    	if($server->getServerLanguage() == Translate::TUR){
    	    $isTurkish = "yes";
    	}elseif($server->getServerLanguage() === null){
    	    if(!file_exists(\pocketmine\DATA . "sunucu.properties") && !file_exists(\pocketmine\DATA . "yoneticiler.json") && !file_exists(\pocketmine\DATA . "beyaz-liste.json")){
                $isTurkish = "no";
    	    }else{
    	        $isTurkish = "yes";
    	    }
    	}
    
    	return $isTurkish;
    }*/
    
    public static function checkTurkish(){
    	$server = Server::getInstance();
    
    	$isTurkish = "no";
    	if(!file_exists(\pocketmine\DATA . "sunucu.properties") && !file_exists(\pocketmine\DATA . "yoneticiler.json") && !file_exists(\pocketmine\DATA . "beyaz-liste.json")){
    	    $isTurkish = "no";
    	}else{
            $isTurkish = "yes";
    	}
    
    	return $isTurkish;
    }
    
}

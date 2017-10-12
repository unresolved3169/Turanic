<?php

/*
 *
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
 *
*/

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LoadPluginCommand extends VanillaCommand {

	/**
	 * LoadPluginCommand constructor.
	 *
	 * @param $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"Load a plugin",
			"/loadplugin <file name or folder name>"
		);
		$this->setPermission("pocketmine.command.loadplugin");
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return false;
		}

		if(count($args) === 0){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->usageMessage);
			return true;
		}

		if(!isset($args[0])) return false;

		$plugin = $sender->getServer()->getPluginManager()->loadPlugin($sender->getServer()->getPluginPath() . DIRECTORY_SEPARATOR . $args[0]);
		if($plugin != null){
			$sender->getServer()->getPluginManager()->enablePlugin($plugin);
			return true;
		}
		return false;
	}
}

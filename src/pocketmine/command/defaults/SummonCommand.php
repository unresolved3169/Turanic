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

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\command\overload\CommandEnum;
use pocketmine\command\overload\CommandParameter;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\math\Vector3;
use pocketmine\nbt\JsonNBTParser;
use pocketmine\nbt\NBT;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;

class SummonCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.summon.description",
			"%pocketmine.command.summon.usage"
		);
		$this->setPermission("pocketmine.command.summon");

		$this->getOverload("default")->setParameter(0, new CommandParameter("entityType", CommandParameter::TYPE_STRING, false, CommandParameter::FLAG_ENUM, new CommandEnum("mob", Entity::getEntityNames())));
		$this->getOverload("default")->setParameter(1, new CommandParameter("spawnPos", CommandParameter::TYPE_POSITION, true));
	}

	public function execute(CommandSender $sender, string $currentAlias, array $args){
		if(!$this->canExecute($sender)){
			return true;
		}

		if(count($args) != 1 and count($args) != 4 and count($args) != 5){
            throw new InvalidCommandSyntaxException();
		}

		$x = $y = $z = 0;
		if(count($args) == 4 or count($args) == 5){  //position is set
			//Code for setting $x
			if(is_numeric($args[1])){                            //x is given directly
				$x = $args[1];
			}elseif(strcmp($args[1], "~") >= 0){    //x is given with a "~"
				$offset_x = trim($args[1], "~");
				if($sender instanceof Player){            //using in-game
					$x = is_numeric($offset_x) ? ($sender->x + $offset_x) : $sender->x;
				}else{                                                            //using in console
					$sender->sendMessage(TextFormat::RED . "You must specify a position where the entity is spawned to when using in console");
					return false;
				}
			}else{                                                                //other circumstances
				$sender->sendMessage(TextFormat::RED . "Argument error");
				return false;
			}

			//Code for setting $y
			if(is_numeric($args[2])){                            //y is given directly
				$y = $args[2];
			}elseif(strcmp($args[2], "~") >= 0){    //y is given with a "~"
				$offset_y = trim($args[2], "~");
				if($sender instanceof Player){            //using in-game
					$y = is_numeric($offset_y) ? ($sender->y + $offset_y) : $sender->y;
					$y = min(128, max(0, $y));
				}else{                                                            //using in console
					$sender->sendMessage(TextFormat::RED . "You must specify a position where the entity is spawned to when using in console");
					return false;
				}
			}else{                                                                //other circumstances
				$sender->sendMessage(TextFormat::RED . "Argument error");
				return false;
			}

			//Code for setting $z
			if(is_numeric($args[3])){                            //z is given directly
				$z = $args[3];
			}elseif(strcmp($args[3], "~") >= 0){    //z is given with a "~"
				$offset_z = trim($args[3], "~");
				if($sender instanceof Player){            //using in-game
					$z = is_numeric($offset_z) ? ($sender->z + $offset_z) : $sender->z;
				}else{                                                            //using in console
					$sender->sendMessage(TextFormat::RED . "You must specify a position where the entity is spawned to when using in console");
					return false;
				}
			}else{                                                                //other circumstances
				$sender->sendMessage(TextFormat::RED . "Argument error");
				return false;
			}
		}    //finish setting the location

		if(count($args) == 1){
			if($sender instanceof Player){
				$x = $sender->x;
				$y = $sender->y;
				$z = $sender->z;
			}else{
				$sender->sendMessage(TextFormat::RED . "You must specify a position where the entity is spawned to when using in console");
				return false;
			}
		} //finish setting the location

		$entity = null;
		$type = $args[0];
		$level = ($sender instanceof Player) ? $sender->getLevel() : $sender->getServer()->getDefaultLevel();
		$nbt = Entity::createBaseNBT(new Vector3($x, $y, $z), null, lcg_value() * 360);
		if(count($args) == 5 and $args[4]{0} == "{"){//Tags are found
			$nbtExtra = JsonNBTParser::parseJSON($args[4]);
			$nbt = NBT::combineCompoundTags($nbt, $nbtExtra, true);
		}

		$entity = Entity::createEntity($type, $level, $nbt);
		if($entity instanceof Entity){
			$entity->spawnToAll();
			$sender->sendMessage("Successfully spawned entity $type at ($x, $y, $z)");
			return true;
		}else{
			$sender->sendMessage(TextFormat::RED . "An error occurred when spawning the entity $type");
			return false;
		}
	}
}

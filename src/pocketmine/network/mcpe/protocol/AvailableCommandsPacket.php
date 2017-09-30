<?php

/*
 *
 *  ____   _  _   __  __ _   __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___   |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|  |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\command\Command;
use pocketmine\command\overload\{CommandParameter, CommandOverload, CommandEnum};

class AvailableCommandsPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::AVAILABLE_COMMANDS_PACKET;
	
	public $commands = [];
	public $postfixes = [];
	
	public function putCommandEnum(CommandEnum $list){
		$this->putString($list->getName());
		$this->putUnsignedVarInt(count($list));
		
		foreach($list->getValues() as $index => $val){
			$this->putByte($index); // TODO others
			$this->putString($val);
		}
	}
	
	public function putCommand(Command $cmd){
		$this->putString($cmd->getName());
		$this->putString($cmd->getDescription());
		$this->putByte(0); // ???
		$this->putByte($cmd->getPermissionLevel());
		$this->putInt(-1); // TODO Aliases
		
		$overloads = $cmd->getOverloads();
		
		$this->putUnsignedVarInt(count($overloads));
		foreach($overloads as $overload){
			$params = $overload->getParameters();
			$this->putUnsignedVarInt(count($params));
			foreach($params as $param){
				$this->putString($param->getName());
				$this->putLInt($param->getFlag() | $param->getType());
				$this->putBool($param->isOptional());
				
				if($param->getFlag() == $param::FLAG_ENUM){
					$this->putCommandEnum($param->getEnum());
				}
			}
		}
	}
	
	protected function encodePayload(){
		$enums = [];
		foreach($this->commands as $c){
			foreach($c->getOverloads() as $ol){
				foreach($ol->getParameters() as $pm){
					if($pm->getFlag() == $pm::FLAG_ENUM){
						$enums[] = $pm->getEnum();
					}
				}
			}
		}
		$enumVals = [];
		foreach($enums as $enum){
			foreach($enum->getValues() as $val){
				$enumVals[] = $val;
			}
		}
		$this->putUnsignedVarInt(count($enumVals));
		foreach($enumVals as $v){
			$this->putString($v);
		}
		
		$this->putUnsignedVarInt(count($this->postfixes));
		foreach($this->postfixes as $pf){
			$this->putString($pf);
		}
		
		$this->putUnsignedVarInt(count($enums));
		foreach($enums as $enum){
			$this->putCommandEnum($enum);
		}
		
		$this->putUnsignedVarInt(count($this->commands));
		foreach($this->commands as $cmd){
			$this->putCommand($cmd);
		}
	}
}
<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
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
use pocketmine\utils\{BinaryStream, Binary};
use pocketmine\command\overload\{CommandParameter, CommandOverload, CommandEnum};

class AvailableCommandsPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::AVAILABLE_COMMANDS_PACKET;
	
	/** @var Command[] */
	public $commands = [];
	
	protected $enumValuesCount = 0;
	
	public function putCommandEnum(CommandEnum $list, BinaryStream $stream){
		$stream->putString($list->getName());
		$stream->putUnsignedVarInt(count($list->getValues()));
		
		foreach($list->getValues() as $index){
			$this->putEnumIndex($index, $stream);
		}
	}
	
	public function putEnumIndex(int $index, BinaryStream $stream){
		if ($this->enumValuesCount < 256) {
			$stream->putByte($index);
		}elseif($this->enumValuesCount < 65536) {
			$stream->putLShort($index);
		}else{
			$stream->putLInt($index);
		}	
	}
	
	protected function getPreparedCommandData(){
		$extraDataStream = new BinaryStream;
		$commandStream = new BinaryStream;
		
		$enumValues = [];
		$enums = [];
		$postfixes = [];
		
		$this->enumValuesCount = 0;
		
		foreach($this->commands as $cmd){
			if($cmd instanceof Command){
				if($cmd->getName() == "help") continue; 
				
				$commandStream->putString($cmd->getName());
				$commandStream->putString($cmd->getDescription());
				$commandStream->putByte(0); // command flags (todo)
				$commandStream->putByte($cmd->getPermissionLevel());
				
				$enumIndex = -1;
				
				if(count($cmd->getAliases()) > 0){
					// recalculate enum indexs
					$aliases = [];
					foreach($cmd->getAliases() as $alias){
						$enumValues[] = $alias;
						$aliases[] = $this->enumValuesCount;
						$this->enumValuesCount++;
					}
					$enum = new CommandEnum($cmd->getName() . "CommandAliases", $aliases);
					$enums[] = $enum;
					$enumIndex = count($enums) - 1;
				}
				
				$commandStream->putLInt($enumIndex);
				
				$overloads = $cmd->getOverloads();
				
				$commandStream->putUnsignedVarInt(count($overloads));
				foreach($overloads as $overload){
					$params = $overload->getParameters();
					$commandStream->putUnsignedVarInt(count($params));
					foreach($params as $param){
						$commandStream->putString($param->getName());
						
						$type = $param->getFlag() | $param->getType();
						if($param->getFlag() == $param::FLAG_ENUM and $param->getEnum() != null){
							$enum = $param->getEnum();
							$realValues = [];
							foreach($enum->getValues() as $v){
								$enumValues[] = $v;
								$realValues[] = $this->enumValuesCount;
								$this->enumValuesCount++;
							}
							$enums[] = new CommandEnum($cmd->getName() . $enum->getName(), $realValues);
							$enumIndex = count($enums) - 1;
							$type |= $enumIndex;
						}elseif($param->getFlag() == $param::FLAG_POSTFIX and strlen($param->getPostfix()) > 0){
							$postfixes[] = $param->getPostfix();
							$type |= count($postfixes) - 1;
						}
						
						$commandStream->putLInt($type);
						$commandStream->putBool($param->isOptional());
						}
					}
			}
		}
		
		$extraDataStream->putUnsignedVarInt($this->enumValuesCount);
		foreach($enumValues as $v){
			$extraDataStream->putString($v);
		}
		
		$extraDataStream->putUnsignedVarInt(count($postfixes));
		foreach($postfixes as $postfix){
			$extraDataStream->putString($postfix);
		}
		
		$extraDataStream->putUnsignedVarInt(count($enums));
		foreach($enums as $enum){
			$this->putCommandEnum($enum, $extraDataStream);
		}
		
		$extraDataStream->putUnsignedVarInt(count($this->commands));
		$extraDataStream->put($commandStream->buffer);
		
		return $extraDataStream->buffer;
	}
	
	protected function encodePayload(){
		$this->put($this->getPreparedCommandData());
	}
}
<?php

namespace pocketmine\command\overload;

use pocketmine\command\Command;

class CommandOverload{
	
	protected $name;
	protected $params = [];
	
	public function __construct(string $name, array $params = []){
		$this->params = $params;
		$this->name = $name;
	}
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getParameters() : array{
		return $this->params;
	}
}
?>
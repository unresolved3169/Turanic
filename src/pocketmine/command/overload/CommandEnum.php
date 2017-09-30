<?php

namespace pocketmine\command\overload;

class CommandEnum{
	
	protected $name;
	protected $values = [];
	
	public function __construct(string $name, array $values = []){
		$this->name = $name;
		$this->values = $values;
	}
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getValues() : array{
		return $this->values;
	}
}
?>
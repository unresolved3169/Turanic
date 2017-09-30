<?php

namespace pocketmine\customUI\windows;

use pocketmine\Player;
use pocketmine\customUI\CustomUI;
use pocketmine\customUI\elements\Button;

class SimpleForm implements CustomUI, \JsonSerializable{

	/** @var string */
	protected $title = '';
	/** @var string */
	protected $content = '';
	/** @var Button[] */
	protected $buttons = [];

	/**
	 * SimpleForm only consists of clickable buttons
	 *
	 * @param string $title
	 * @param string $content
	 */
	public function __construct($title, $content = ''){
		$this->title = $title;
		$this->content = $content;
	}

	/**
	 * Add button to form
	 *
	 * @param Button $button
	 */
	public function addButton(Button $button){
		$this->buttons[] = $button;
	}

	final public function jsonSerialize(){
		$data = [
			'type' => 'form',
			'title' => $this->title,
			'content' => $this->content,
			'buttons' => []
		];
		foreach ($this->buttons as $button){
			$data['buttons'][] = $button;
		}
		return $data;
	}

	/**
	 * To handle manual closing
	 * @param Player $player
	 */
	public function close(Player $player){
	}

	/**
	 * @param int $response Button index
	 * @param Player $player
	 * @return string containing the value of the clicked button
	 * @throws Exception
	 */
	public function handle($response, Player $player){
		$return = "";
		if (isset($this->buttons[$response])){
			if (!is_null($value = $this->buttons[$response]->handle($response, $player))) $return = $value;
		} else{
			error_log(__CLASS__ . '::' . __METHOD__ . " Button with index {$response} doesn't exists.");
		}
		return $return;
	}

	final public function getTitle(){
		return $this->title;
	}

	public function getContent(): array{
		return [$this->content, $this->buttons];
	}
}
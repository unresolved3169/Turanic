<?php

namespace pocketmine\customUI\windows;

use pocketmine\Player;
use pocketmine\customUI\CustomUI;
use pocketmine\customUI\elements\UIElement;

class CustomForm implements CustomUI, \JsonSerializable{

	/** @var string */
	protected $title = '';
	/** @var UIElement[] */
	protected $elements = [];
	/** @var string Only for server settings */
	protected $iconURL = '';

	/**
	 * CustomForm is a totally custom and dynamic form
	 * @param $title
	 */
	public function __construct($title){
		$this->title = $title;
	}

	/**
	 * Add element to form
	 * @param UIElement $element
	 */
	public function addElement(UIElement $element){
		$this->elements[] = $element;
	}

	/**
	 * Only for server settings
	 * @param string $url
	 */
	public function setIconUrl($url){
		$this->iconURL = $url;
	}

	final public function jsonSerialize(){
		$data = [
			'type' => 'custom_form',
			'title' => $this->title,
			'content' => []
		];
		if ($this->iconURL != ''){
			$data['icon'] = [
				"type" => "url",
				"data" => $this->iconURL
			];
		}
		foreach ($this->elements as $element){
			$data['content'][] = $element;
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
	 * @param array $response
	 * @param Player $player
	 * @return array containing the options, data, responses etc
	 */
	public function handle($response, Player $player){
		foreach ($response as $elementKey => $elementValue){
			if (isset($this->elements[$elementKey])){
				$this->elements[$elementKey]->handle($elementValue, $player);
			} else{
				error_log(__CLASS__ . '::' . __METHOD__ . " Element with index {$elementKey} doesn't exists.");
			}
		}

		$return = [];
		foreach ($response as $elementKey => $elementValue){
			if (isset($this->elements[$elementKey])){
				if (!is_null($value = $this->elements[$elementKey]->handle($elementValue, $player))) $return[] = $value;
			}
		}
		return $return;
	}

	final public function getTitle(){
		return $this->title;
	}

	public function getContent(): array{
		return $this->elements;
	}
}
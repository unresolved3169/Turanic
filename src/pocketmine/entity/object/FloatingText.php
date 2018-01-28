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

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\Player;
use pocketmine\utils\TextUtils;
use pocketmine\utils\UUID;
use pocketmine\item\Item as ItemItem;

class FloatingText extends Entity {

    protected $text = "";
    protected $title = "";

    protected function initEntity(){
        parent::initEntity();

        $this->setTitle($this->namedtag["Title"] ?? "");
        $this->setText($this->namedtag["Text"] ?? "");

        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->setScale(0.01);
    }

    /**
     * @return string
     */
    public function getTitle() : string{
        return $this->title;
    }

    public function setText(string $text, bool $center = false){
        $this->text = $text;

        $this->updateNameTag($center);
    }

    public function setTitle(string $text, bool $center = false){
        $this->title = $text;

        $this->updateNameTag($center);
    }

    public function center(){
        $text = $this->getNameTag();

        $this->setNameTag(TextUtils::center($text));
    }

    public function updateNameTag(bool $center = false){
        $text = $this->title . ($this->text !== "" ? "\n$this->text" : "");
        if($center) TextUtils::center($text);
        $this->setNameTag($text);
    }

    public function saveNBT(){
        parent::saveNBT();

        $this->namedtag->Title = new StringTag("Title", $this->title);
        $this->namedtag->Text = new StringTag("Text", $this->text);
    }

    public function onUpdate(int $currentTick){
        return false;
    }

    public function canCollideWith(Entity $entity) : bool{
        return false;
    }

    protected function sendSpawnPacket(Player $player){
        $pk = new AddPlayerPacket();
        $pk->uuid = UUID::fromRandom();
        $pk->username = "";
        $pk->entityRuntimeId = $this->id;
        $pk->position = $this->asVector3();
        $pk->item = ItemItem::get(ItemItem::AIR, 0, 0);
        $pk->metadata = $this->propertyManager->getAll();
        $player->dataPacket($pk);
    }
}
<?php

namespace pocketmine\inventory;

use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\Player;
use pocketmine\Server;

class PlayerInventory extends HumanInventory {

    /** @var Player */
    protected $holder;

    const CURSOR_INDEX = -1;
    const CREATIVE_INDEX = -2;
    const CRAFT_INDEX_0 = -3;
    const CRAFT_INDEX_1 = -4;
    const CRAFT_INDEX_2 = -5;
    const CRAFT_INDEX_3 = -6;
    const CRAFT_INDEX_4 = -7;
    const CRAFT_INDEX_5 = -8;
    const CRAFT_INDEX_6 = -9;
    const CRAFT_INDEX_7 = -10;
    const CRAFT_INDEX_8 = -11;
    const CRAFT_RESULT_INDEX = -12;

    const OFFHAND_ARMOR_SLOT_ID = 4;

    /** @var Item */
    protected $cursor;
    /** @var Item[] */
    protected $craftSlots = [ 0 => null, 1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null, 8 => null ];
    /** @var Item */
    protected $craftResult = null;

    public function setItem($index, Item $item, $sendPacket = true) {
        if ($index >= 0) {
            return parent::setItem($index, $item, $sendPacket);
        }
        switch ($index) {
            case self::CURSOR_INDEX:
                $this->cursor = clone $item;
                if ($sendPacket) {
                    $this->sendCursor();
                }
                break;
            case self::CRAFT_INDEX_0:
            case self::CRAFT_INDEX_1:
            case self::CRAFT_INDEX_2:
            case self::CRAFT_INDEX_3:
            case self::CRAFT_INDEX_4:
            case self::CRAFT_INDEX_5:
            case self::CRAFT_INDEX_6:
            case self::CRAFT_INDEX_7:
            case self::CRAFT_INDEX_8:
                $slot = self::CRAFT_INDEX_0 - $index;
                $this->craftSlots[$slot] = $item;
                if ($sendPacket) {
                    /** @todo add packet sending */
                    $pk = new InventorySlotPacket();
                    $pk->containerId = ProtocolInfo::CONTAINER_ID_NONE;
                    $pk->slot = 0;
                    $pk->item = Item::get(Item::WOOL, 10);
                    $this->holder->dataPacket($pk);
                }
                break;
            case self::CRAFT_RESULT_INDEX:
                $this->craftResult = $item;
                /*if ($sendPacket) {
                    @todo add packet sending
                }*/
                break;
        }
        return true;
    }

    public function getItem($index) {
        if ($index < 0) {
            switch ($index) {
                case self::CURSOR_INDEX:
                    return $this->cursor == null ? clone $this->air : clone $this->cursor;
                case self::CRAFT_INDEX_0:
                case self::CRAFT_INDEX_1:
                case self::CRAFT_INDEX_2:
                case self::CRAFT_INDEX_3:
                case self::CRAFT_INDEX_4:
                case self::CRAFT_INDEX_5:
                case self::CRAFT_INDEX_6:
                case self::CRAFT_INDEX_7:
                case self::CRAFT_INDEX_8:
                    $slot = self::CRAFT_INDEX_0 - $index;
                    return $this->craftSlots[$slot] == null ? clone $this->air : clone $this->craftSlots[$slot];
                case self::CRAFT_RESULT_INDEX:
                    return $this->craftResult == null ? clone $this->air : clone $this->craftResult;
            }
        }
        return parent::getItem($index);
    }

    public function setHotbarSlotIndex($index, $slot) {
        if ($index == $slot || $slot < 0) {
            return;
        }
        $tmp = $this->getItem($index);
        $this->setItem($index, $this->getItem($slot));
        $this->setItem($slot, $tmp);
    }

    public function sendSlot($index, $target) {
        $pk = new InventorySlotPacket();
        $pk->containerId = ProtocolInfo::CONTAINER_ID_INVENTORY;
        $pk->slot = $index;
        $pk->item = $this->getItem($index);
        $this->holder->dataPacket($pk);
    }

    public function sendContents($target) {
        $pk = new InventoryContentPacket();
        $pk->inventoryID = ProtocolInfo::CONTAINER_ID_INVENTORY;
        $pk->items = [];
        $mainPartSize = $this->getSize();
        for ($i = 0; $i < $mainPartSize; $i++) { //Do not send armor by error here
            $pk->items[$i] = $this->getItem($i);
        }
        $this->holder->dataPacket($pk);
        $this->sendCursor();
    }

    public function sendCursor() {
        $pk = new InventorySlotPacket();
        $pk->containerId = ProtocolInfo::CONTAINER_ID_CURSOR_SELECTED;
        $pk->slot = 0;
        $pk->item = $this->cursor;
        $this->holder->dataPacket($pk);
    }

    /**
     *
     * @param integer $index
     * @param Player[] $target
     */
    public function sendArmorSlot($index, $target){
        if ($target instanceof Player) {
            $target = [$target];
        }

        if ($index - $this->getSize() == self::OFFHAND_ARMOR_SLOT_ID) {
            $this->sendOffHandContents($target);
        } else {
            $armor = $this->getArmorContents();
            $pk = new MobArmorEquipmentPacket();
            $pk->eid = $this->holder->getId();
            $pk->slots = $armor;
            foreach($target as $player){
                if ($player === $this->holder) {
                    /** @var Player $player */
                    $pk2 = new InventorySlotPacket();
                    $pk2->containerId = ProtocolInfo::CONTAINER_ID_ARMOR;
                    $pk2->slot = $index - $this->getSize();
                    $pk2->item = $this->getItem($index);
                    $player->dataPacket($pk2);
                } else {
                    $player->dataPacket($pk);
                }
            }
        }
    }
    public function sendArmorContents($target) {
        if ($target instanceof Player) {
            $target = [$target];
        }
        $armor = $this->getArmorContents();
        $pk = new MobArmorEquipmentPacket();
        $pk->eid = $this->holder->getId();
        $pk->slots = $armor;
        foreach ($target as $player) {
            if ($player === $this->holder) {
                $pk2 = new InventoryContentPacket();
                $pk2->inventoryID = ProtocolInfo::CONTAINER_ID_ARMOR;
                $pk2->items = $armor;
                $player->dataPacket($pk2);
            } else {
                $player->dataPacket($pk);
            }
        }
    }

    /**
     * @param Player[] $targets
     */
    private function sendOffHandContents($targets) {
        $pk = new MobEquipmentPacket();
        $pk->eid = $this->getHolder()->getId();
        $pk->item = $this->getItem($this->getSize() + self::OFFHAND_ARMOR_SLOT_ID);
        $pk->slot = $this->getHeldItemSlot();
        $pk->selectedSlot = $this->getHeldItemIndex();
        $pk->windowId = MobEquipmentPacket::WINDOW_ID_PLAYER_OFFHAND;
        foreach ($targets as $player) {
            if ($player === $this->getHolder()) {
                $pk2 = new InventorySlotPacket();
                $pk2->containerId = ProtocolInfo::CONTAINER_ID_OFFHAND;
                $pk2->slot = 0;
                $pk2->item = $this->getItem($this->getSize() + self::OFFHAND_ARMOR_SLOT_ID);
                $player->dataPacket($pk2);
            } else {
                $player->dataPacket($pk);
            }
        }
    }

    /**
     *
     * @return Item[]
     */
    public function getCraftContents() {
        return $this->craftSlots;
    }

    /**
     *
     * @param integer $slotIndex
     * @return boolean
     */
    protected function isArmorSlot($slotIndex) {
        return $slotIndex >= $this->getSize();
    }

    /**
     *
     * @param integer $slotIndex
     * @return boolean
     */
    public function clear($slotIndex) {
        if (isset($this->slots[$slotIndex])) {
            if ($this->isArmorSlot($slotIndex)) { //Armor change
                $ev = new EntityArmorChangeEvent($this->holder, $this->slots[$slotIndex], clone $this->air, $slotIndex);
                Server::getInstance()->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->sendArmorSlot($slotIndex, [$this->holder]);
                    return false;
                }
            } else {
                $ev = new EntityInventoryChangeEvent($this->holder, $this->slots[$slotIndex], clone $this->air, $slotIndex);
                Server::getInstance()->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->sendSlot($slotIndex, $this->holder);
                    return false;
                }
            }
            $oldItem = $this->slots[$slotIndex];
            $newItem = $ev->getNewItem();
            if ($newItem->getId() !== Item::AIR) {
                $this->slots[$slotIndex] = clone $newItem;
            } else {
                unset($this->slots[$slotIndex]);
            }
            $this->onSlotChange($slotIndex, $oldItem);
        }
        return true;
    }
}
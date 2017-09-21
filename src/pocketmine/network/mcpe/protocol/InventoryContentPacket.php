<?php

namespace pocketmine\network\mcpe\protocol;

class InventoryContentPacket extends DataPacket {

    const NETWORK_ID = ProtocolInfo::INVENTORY_CONTENT_PACKET;

    public $inventoryID;
    public $items;

    const CONTAINER_ID_NONE = -1;
    const CONTAINER_ID_INVENTORY = 0;
    const CONTAINER_ID_FIRST = 1;
    const CONTAINER_ID_LAST = 100;
    const CONTAINER_ID_OFFHAND = 119;
    const CONTAINER_ID_ARMOR = 120;
    const CONTAINER_ID_CREATIVE = 121;
    const CONTAINER_ID_SELECTION_SLOTS = 122;
    const CONTAINER_ID_FIXEDINVENTORY = 123;
    const CONTAINER_ID_CURSOR_SELECTED = 124;

    public function decode() {
        var_dump("decode: ".__CLASS__);
    }
    public function encode() {
        $this->reset();
        $this->putVarInt($this->inventoryID);
        $itemsNum = count($this->items);
        $this->putVarInt($itemsNum);
        for ($i = 0; $i < $itemsNum; $i++) {
            $this->putSlot($this->items[$i]);
        }
    }

    public function getName(){
        return "InventoryContentPacket";
    }
}
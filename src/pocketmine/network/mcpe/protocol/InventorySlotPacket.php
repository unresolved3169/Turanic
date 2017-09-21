<?php
/**
 * Created by PhpStorm.
 * User: Enes
 * Date: 21.09.2017
 * Time: 09:40
 */

namespace pocketmine\network\mcpe\protocol;

class InventorySlotPacket extends DataPacket {

    const NETWORK_ID = ProtocolInfo::INVENTORY_SLOT_PACKET;

    public $containerId;
    public $slot;
    public $item;

    public function decode() {
        var_dump('decode: ' . __CLASS__);
    }
    public function encode() {
        $this->reset();
        $this->putVarInt($this->containerId);
        $this->putVarInt($this->slot);
        $this->putSlot($this->item);
    }

}
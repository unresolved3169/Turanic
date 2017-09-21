<?php

namespace pocketmine\network\mcpe\protocol;

class PlayerHotbarPacket extends DataPacket {

    const NETWORK_ID = ProtocolInfo::PLAYER_HOTBAR_PACKET;

    public $selectedSlot;
    public $slotsLink;

    public function decode() {
        var_dump(bin2hex($this->buffer));
    }
    public function encode() {
        $this->reset();
        $this->putVarInt($this->selectedSlot);
        $slotsNum = count($this->slotsLink);
        $this->putVarInt($slotsNum);
        for ($i = 0; $i < $slotsNum; $i++) {
            $this->putVarInt($this->slotsLink[$i]);
        }
    }

}
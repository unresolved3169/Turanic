<?php

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class PingPacket extends DataPacket{

    const NETWORK_ID = ProtocolInfo::PING_PACKET;

    public $ping;

    public function decodePayload() {
        $this->ping = $this->getVarInt();
    }

    public function encodePayload() {
        $this->reset();
        $this->putVarInt($this->ping);
    }
}
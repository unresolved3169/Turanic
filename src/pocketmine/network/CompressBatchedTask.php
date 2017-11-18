<?php

declare(strict_types=1);

namespace pocketmine\network;

use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class CompressBatchedTask extends AsyncTask{

    public $level = 7;
    public $data;
    public $targets;

    /**
     * @param BatchPacket $batch
     * @param string[]    $targets
     */
    public function __construct(BatchPacket $batch, array $targets){
        $this->data = $batch->payload;
        $this->targets = serialize($targets);
        $this->level = $batch->getCompressionLevel();
    }

    public function onRun(){
        $batch = new BatchPacket();
        $batch->payload = $this->data;
        $this->data = null;

        $batch->setCompressionLevel($this->level);
        $batch->encode();

        $this->setResult($batch->buffer, false);
    }

    public function onCompletion(Server $server){
        $pk = new BatchPacket($this->getResult());
        $pk->isEncoded = true;
        $server->broadcastPacketsCallback($pk, unserialize($this->targets));
    }
}
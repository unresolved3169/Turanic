<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\network\SourceInterface;

class NetworkInterfaceEvent extends ServerEvent{
    /** @var SourceInterface */
    protected $interface;

    /**
     * @param SourceInterface $interface
     */
    public function __construct(SourceInterface $interface){
        $this->interface = $interface;
    }

    /**
     * @return SourceInterface
     */
    public function getInterface() : SourceInterface{
        return $this->interface;
    }
}
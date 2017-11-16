<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\network\SourceInterface;

/**
 * Called when a network interface crashes, with relevant crash information.
 */
class NetworkInterfaceCrashEvent extends NetworkInterfaceEvent{
    public static $handlerList = null;

    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct(SourceInterface $interface, \Throwable $throwable){
        parent::__construct($interface);
        $this->exception = $throwable;
    }

    /**
     * @return \Throwable
     */
    public function getCrashInformation() : \Throwable{
        return $this->exception;
    }
}
<?php

declare(strict_types=1);

namespace pocketmine\event\server;

/**
 * Called when a network interface is unregistered
 */
class NetworkInterfaceUnregisterEvent extends NetworkInterfaceEvent{
    public static $handlerList = null;

}
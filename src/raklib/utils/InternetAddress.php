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

namespace raklib\utils;

class InternetAddress{

    /**
     * @var string
     */
    public $ip;
    /**
     * @var int
     */
    public $port;
    /**
     * @var int
     */
    public $version;

    public function __construct(string $address, int $port, int $version){
        $this->ip = $address;
        if($port < 0 or $port > 65536){
            throw new \InvalidArgumentException("Invalid port range");
        }
        $this->port = $port;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getIp() : string{
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort() : int{
        return $this->port;
    }

    /**
     * @return int
     */
    public function getVersion() : int{
        return $this->version;
    }

    public function __toString(){
        return $this->ip . ":" . $this->port;
    }
}
<?php

/*
 *
 *  _____            _               _____           
 * / ____|          (_)             |  __ \          
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___  
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \ 
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/ 
 *                         __/ |                    
 *                        |___/                     
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Turanic
 * @link https://github.com/Turanic/Turanic
 *
 *
*/

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\utils\Binary;
use pocketmine\utils\UUID;

class LoginPacket extends DataPacket {

	const NETWORK_ID = ProtocolInfo::LOGIN_PACKET;

	const MOJANG_PUBKEY = "MHYwEAYHKoZIzj0CAQYFK4EEACIDYgAE8ELkixyLcwlZryUQcu1TvPOmI2B7vX83ndnWRUaXm74wFfa5f/lwQNTfrLVHa2PmenpGI6JhIMUJaWZrjmMj90NoKNFSNBuKdm8rYiXsfaz3K36x/1U26HpG0ZxK/V1V";

	const EDITION_POCKET = 0;


    public $username;
    public $protocol;
    public $clientId;
    public $clientUUID;
    public $serverAddress;
    public $clientSecret;
    public $slim = false;
    public $skinId;
    public $chainsDataLength;
    public $chains;
    public $playerDataLength;
    public $playerData;
    public $isValidProtocol = true;
    public $inventoryType = -1;
    public $deviceOS = -1;
    public $deviceModel = "";
    public $xuid = '';
    public $languageCode = 'unknown';
    public $clientVersion = 'unknown';
    public $skinGeometryName = "";
    public $skinGeometryData = "";
    public $capeData = "";

    private function getFromString(&$body, $len) {
        $res = substr($body, 0, $len);
        $body = substr($body, $len);
        return $res;
    }

    public function decode() {
        $tmpData = Binary::readInt(substr($this->buffer, 1, 4));
        if ($tmpData == 0) {
            $this->getShort();
        }
        $this->protocol = $this->getInt();
        if ($this->protocol < 120) {
            $this->getByte();
        }
        $data = $this->getString();

        if (ord($data{0}) != 120 || (($decodedData = @zlib_decode($data)) === false)) {
            $body = $data;
        } else {
            $body = $decodedData;
        }

        $this->chainsDataLength = Binary::readLInt($this->getFromString($body, 4));
        $this->chains = json_decode($this->getFromString($body, $this->chainsDataLength), true);
        $this->playerDataLength = Binary::readLInt($this->getFromString($body, 4));
        $this->playerData = $this->getFromString($body, $this->playerDataLength);

        $this->chains['data'] = array();
        $index = 0;
        foreach ($this->chains['chain'] as $key => $jwt) {
            $data = self::load($jwt);
            if (isset($data['extraData'])) {
                $dataIndex = $index;
            }
            $this->chains['data'][$index] = $data;
            $index++;
        }
        if (!isset($dataIndex)) {
            $this->isValidProtocol = false;
            return;
        }

        $this->playerData = self::load($this->playerData);
        $this->username = $this->chains['data'][$dataIndex]['extraData']['displayName'];
        $this->clientId = $this->chains['data'][$dataIndex]['extraData']['identity'];
        $this->clientUUID = UUID::fromString($this->chains['data'][$dataIndex]['extraData']['identity']);
        $this->identityPublicKey = $this->chains['data'][$dataIndex]['identityPublicKey'];
        if (isset($this->chains['data'][$dataIndex]['extraData']['XUID'])) {
            $this->xuid = $this->chains['data'][$dataIndex]['extraData']['XUID'];
        }

        $this->serverAddress = $this->playerData['ServerAddress'];
        $this->skinId = $this->playerData['SkinId'];
        $this->skin = base64_decode($this->playerData['SkinData']);
        if (isset($this->playerData['SkinGeometryName'])) {
            $this->skinGeometryName = $this->playerData['SkinGeometryName'];
        }
        if (isset($this->playerData['SkinGeometry'])) {
            $this->skinGeometryData = base64_decode($this->playerData['SkinGeometry']);
        }
        $this->clientSecret = $this->playerData['ClientRandomId'];
        if (isset($this->playerData['DeviceOS'])) {
            $this->deviceOS = $this->playerData['DeviceOS'];
        }
        if (isset($this->playerData['DeviceModel'])) {
            $this->deviceModel = $this->playerData['DeviceModel'];
        }
        if (isset($this->playerData['UIProfile'])) {
            $this->inventoryType = $this->playerData['UIProfile'];
        }
        if (isset($this->playerData['LanguageCode'])) {
            $this->languageCode = $this->playerData['LanguageCode'];
        }
        if (isset($this->playerData['GameVersion'])) {
            $this->clientVersion = $this->playerData['GameVersion'];
        }
        if (isset($this->playerData['CapeData'])) {
            $this->capeData = base64_decode($this->playerData['CapeData']);
        }
    }

    public function encode(){
    }

    public static function load($jwsTokenString) {
        $parts = explode('.', $jwsTokenString);
        if (isset($parts[1])) {
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            return $payload;
        }
        return "";
    }

}

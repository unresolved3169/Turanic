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
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Turanic
 * @link https://github.com/Turanic/Turanic
 *
 *
*/
/**
 * Minecraft: PE multiplayer protocol implementation
 */

namespace pocketmine\network\mcpe\protocol;
interface ProtocolInfo {
	/**
	 * Actual Minecraft: PE protocol version
	 */

	const CURRENT_PROTOCOL = 137;
	const ACCEPTED_PROTOCOLS = [137];
	const MINECRAFT_VERSION = ["v1.2.0"];
	const MINECRAFT_VERSION_NETWORK = "1.2.0";

    const LOGIN_PACKET = 0x01;
    const PLAY_STATUS_PACKET = 0x02;
    const SERVER_TO_CLIENT_HANDSHAKE_PACKET = 0x03;
    const CLIENT_TO_SERVER_HANDSHAKE_PACKET = 0x04;
    const DISCONNECT_PACKET = 0x05;
    const RESOURCE_PACKS_INFO_PACKET = 0x06;
    const RESOURCE_PACKS_STACK_PACKET = 0x07;
    const RESOURCE_PACKS_CLIENT_RESPONSE_PACKET = 0x08;
    const TEXT_PACKET = 0x09;
    const SET_TIME_PACKET = 0x0a;
    const START_GAME_PACKET = 0x0b;
    const ADD_PLAYER_PACKET = 0x0c;
    const ADD_ENTITY_PACKET = 0x0d;
    const REMOVE_ENTITY_PACKET = 0x0e;
    const ADD_ITEM_ENTITY_PACKET = 0x0f;
    const ADD_HANGING_ENTITY_PACKET = 0x10;
    const TAKE_ITEM_ENTITY_PACKET = 0x11;
    const MOVE_ENTITY_PACKET = 0x12;
    const MOVE_PLAYER_PACKET = 0x13;
    const UPDATE_BLOCK_PACKET = 0x15;
    const ADD_PAINTING_PACKET = 0x16;
    const EXPLODE_PACKET = 0x17;
    const LEVEL_EVENT_PACKET = 0x19;
    const TILE_EVENT_PACKET = 0x1a;
    const ENTITY_EVENT_PACKET = 0x1b;
    const MOB_EFFECT_PACKET = 0x1c;
    const UPDATE_ATTRIBUTES_PACKET = 0x1d;
    const INVENTORY_TRANSACTION_PACKET = 0x1e;
    const MOB_EQUIPMENT_PACKET = 0x1f;
    const MOB_ARMOR_EQUIPMENT_PACKET = 0x20;
    const INTERACT_PACKET = 0x21;
    const BLOCK_PICK_REQUEST_PACKET = 0x22;
    const ENTIRY_PICK_REQUEST_PACKET = 0x23;
    const PLAYER_ACTION_PACKET = 0x24;
    const HURT_ARMOR_PACKET = 0x26;
    const SET_ENTITY_DATA_PACKET = 0x27;
    const SET_ENTITY_MOTION_PACKET = 0x28;
    const SET_ENTITY_LINK_PACKET = 0x29;
    const SET_HEALTH_PACKET = 0x2a;
    const SET_SPAWN_POSITION_PACKET = 0x2b;
    const ANIMATE_PACKET = 0x2c;
    const RESPAWN_PACKET = 0x2d;
    const CONTAINER_OPEN_PACKET = 0x2e;
    const CONTAINER_CLOSE_PACKET = 0x2f;
    const PLAYER_HOTBAR_PACKET = 0x30;
    const INVENTORY_CONTENT_PACKET = 0x31;
    const INVENTORY_SLOT_PACKET = 0x32;
    const CONTAINER_SET_DATA_PACKET = 0x33;
    const CRAFTING_DATA_PACKET = 0x34;
    const CRAFTING_EVENT_PACKET = 0x35;
    const GUI_DATA_PICK_ITEM_PACKET = 0x36;
    const ADVENTURE_SETTINGS_PACKET = 0x37;
    const TILE_ENTITY_DATA_PACKET = 0x38;
    const FULL_CHUNK_DATA_PACKET = 0x3a;
    const SET_COMMANDS_ENABLED_PACKET = 0x3b;
    const SET_DIFFICULTY_PACKET = 0x3c;
    const SET_PLAYER_GAMETYPE_PACKET = 0x3e;
    const PLAYER_LIST_PACKET = 0x3f;
    const CLIENTBOUND_MAP_ITEM_DATA_PACKET = 0x43;
    const MAP_INFO_REQUEST_PACKET = 0x44;
    const REQUEST_CHUNK_RADIUS_PACKET = 0x45;
    const CHUNK_RADIUS_UPDATE_PACKET = 0x46;
    const AVAILABLE_COMMANDS_PACKET = 0x4c;
    const COMMAND_STEP_PACKET = 0x4d;
    const RESOURCE_PACK_DATA_INFO_PACKET = 0x51;
    const TRANSFER_PACKET = 0x54;
    const PLAY_SOUND_PACKET = 0x55;
    const STOP_SOUND_PACKET = 0x56;
    const SET_TITLE_PACKET = 0x57;
    const ADD_BEHAVIOR_TREE_PACKET = 0x58;
    const STRUCTURE_BLOCK_UPDATE_PACKET = 0x59;
    const PLAYER_SKIN_PACKET = 0x5c;
    const SET_LAST_HURT_BY_PACKET = 0x5d;
}

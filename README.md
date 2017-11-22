<img src="https://i.imgur.com/jw0b3BH.png" border="0">

Fast server software for Minecraft: Bedrock/PE Edition and W10 Edition - v1.2.x.
Turanic is a fork of GenisysPro

| Status | Travis CI | Discord |
| :---: | :---: | :---: |
 Near to be Stable | [![Build Status](https://travis-ci.org/TuranicTeam/Turanic.svg?branch=master)](https://travis-ci.org/TuranicTeam/Turanic) | [![Discord](https://camo.githubusercontent.com/455152269a0ed38255ed15e375084d4dd08e0c98/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f636861742d6f6e253230646973636f72642d3732383944412e737667)](https://discord.gg/4GZxrdk) |

-------------
## Can I test this before I download it?
Yes, you can. There are our member who already use it, and at times there are dev servers running and you might be lucky enough to get in..<br>


IP: **play.fylkat.cz**  
Port: **19132**

-------------

## Known Bugs in 1.2:

- Movement Bug (players not affected by gravity in some server devices)

## Features of Turanic:
- Soft Codes
- Better Than other PocketMine-Forks about lag problem
- Fast Packet Serializing with Worker
- More Blocks / Items
 - Jukebox
 - MusicDiscs
- Support Some PmmpApi and Some GenApi
- Mob Behavior System (MobAI) (Optional)
- Added some sounds/particles and methods for Player
- Support 32 & 64 bit systems (32bit buggy on mcbe1.2)
- Command Overloads,Parameters system in 1.2 (AvailableCommandsPacket)
- And more...

### TODO List (we will be continue after fully 1.2 update):
- [x] **Optimization and Stability**
- [x] **Virtual Inventory** 
- [ ] **Fully MobAI (indev)**
- [ ] **Ultra Fast Chunk Load/Unload/Generate**
- [ ] **More thread/worker**
- [x] **Command Parameters**
- [ ] **Basic ChunkLoader System**
- [ ] **Fast NBT Writer/reader**
- [ ] **Basic Redstone system (without lag) (indev)**
- [ ] **Add All New Blocks in 1.2(indev)**

# License:
```
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
```

# Third-party Libraries/Protocols Used:
* __[PHP Sockets](http://php.net/manual/en/book.sockets.php)__
* __[PHP mbstring](http://php.net/manual/en/book.mbstring.php)__
* __[PHP SQLite3](http://php.net/manual/en/book.sqlite3.php)__
* __[PHP BCMath](http://php.net/manual/en/book.bc.php)__
* __[PHP pthreads](http://pthreads.org/)__: Threading for PHP - Share Nothing, Do Everything.
* __[PHP YAML](https://code.google.com/p/php-yaml/)__: The Yaml PHP Extension provides a wrapper to the LibYAML library.
* __[LibYAML](http://pyyaml.org/wiki/LibYAML)__: A YAML 1.1 parser and emitter written in C.
* __[cURL](http://curl.haxx.se/)__: cURL is a command line tool for transferring data with URL syntax
* __[Zlib](http://www.zlib.net/)__: A Massively Spiffy Yet Delicately Unobtrusive Compression Library
* __[Source RCON Protocol](https://developer.valvesoftware.com/wiki/Source_RCON_Protocol)__
* __[UT3 Query Protocol](http://wiki.unrealadmin.org/UT3_query_protocol)__
* __[PHP OpenSSL](http://php.net/manual/en/book.openssl.php)__: Cryptography and SSL/TLS Toolkit

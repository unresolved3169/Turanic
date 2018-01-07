[![image](https://i.hizliresim.com/1GAdvN.png)](https://github.com/TuranicTeam/Turanic)

Fast server software for Minecraft: Bedrock/PE Edition and W10 Edition - v1.2.x.
Turanic is a fork of GenisysPro

| Phar | Twitter | Travis CI | Discord | Hit |
| :---: | :---: | :---: | :---: | :---: |
 [![Download](https://img.shields.io/badge/download-latest-blue.svg)](https://jenkins.zxda.net/job/Turanic/) | [![Twitter](https://i.hizliresim.com/vJ2594.jpg)](https://twitter.com/TuranicTeam) | [![Build Status](https://travis-ci.org/TuranicTeam/Turanic.svg?branch=master)](https://travis-ci.org/TuranicTeam/Turanic) | [![Discord](https://camo.githubusercontent.com/455152269a0ed38255ed15e375084d4dd08e0c98/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f636861742d6f6e253230646973636f72642d3732383944412e737667)](https://discord.gg/4GZxrdk) | [![HitCount](http://hits.dwyl.io/TuranicTeam/Turanic.svg)](http://hits.dwyl.io/TuranicTeam/Turanic) |

-------------
## Can I test it before I download it?
Yes, you can. There are our member who already use it, and at times there are dev servers running and you might be lucky enough to get in..<br>

IP: **kitpvp.pkpvp.cf**  
Port: **19172**

-------------

## Known Bugs in 1.2:
- Movement Bug (players not affected by gravity in some server devices)

# Finished & Planned Features
 - Worlds
  - [x] Dimensions
    - [x] Nether Dimension
    - [x] End Dimension
    - [x] Fully Functional Nether Portal Frame and Block
    - [x] Funtional END_PORTAL Block (Portal Soon)
  - [x] Weather System
  - [ ] Temperature System
 - Blocks
   - [x] EndPortal
   - [x] Portal (Nether Portal Block)
   - [x] DragonEgg
   - [x] Slime Block
   - [x] Monster Spawner
 - Tiles
   - [x] Beacon
   - [x] Brewing Stand
   - [x] Command Block
   - [x] Daylight Detector
   - [x] Dispenser
   - [x] Dropper
   - [x] Hopper
   - [x] Jukebox
   - [x] Mob Spawner
   - [x] Shulker Box
 - Items
   - [x] Vanilla Enchants (Progress: 98%)
   - [x] Splash Potions
   - [ ] FireCharge
   - [x] Totem of Undying
   - [x] Elytra Wings
   - [ ] Firework Rocket (Progress: 95%)
   - [x] Lingering Potions
 - Commands
   - [x] ClearInventory Command
   - [ ] PlaySound Command
 - Utils
   - [x] TextUtils::center like PC or Minet.
   - [x] TextFormat::randomize 
 - Others
   - [x] Virtual Inventory
   - [x] Command Parameters
   - [x] Advanced Creative Items
   - [ ] Fully MobAI (indev)
   - [ ] Ultra Fast Chunk Load/Unload/Generate
   - [ ] More thread/worker
   - [ ] Basic ChunkLoader System
   - [ ] Fast NBT Writer/reader
   - [ ] Basic Redstone system (without lag) (Progress: %80)
   - [ ] Update languages
<br />***More to do...***

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

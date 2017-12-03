<?php

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class CommandBlock extends Spawnable implements Nameable,CommandSender {

    const NORMAL = 0;
    const REPEATING = 1;
    const CHAIN = 2;

    private $permission;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);

        if(!isset($nbt->Command)){
            $nbt->Command = new StringTag("Command", ""); // komut
        }
        if(!isset($nbt->blockType)){
            $nbt->blockType = new IntTag("blockType", self::NORMAL); // komut bloğu tipi
        }
        if(!isset($nbt->SuccessCount)){
            $nbt->SuccessCount = new IntTag("SuccessCount", 0); // başarı sayısı
        }
        if(!isset($nbt->LastOutput)){
            $nbt->LastOutput = new StringTag("LastOutput", ""); // son çıkış
        }
        if(!isset($nbt->TrackOutput)){
            $nbt->TrackOutput = new ByteTag("TrackOutput", 0); // komut çıkışı
        }
        if(!isset($nbt->powered)){
            $nbt->powered = new ByteTag("powered", 0); // redstone
        }
        if(!isset($nbt->conditionMet)){
            $nbt->conditionMet = new ByteTag("conditionMet", 0); // koşul
        }
        if(!isset($nbt->UpdateLastExecution)){
            $nbt->UpdateLastExecution = new ByteTag("UpdateLastExecution", 0); // sadece bir kez çalışsın
        }
        if(!isset($nbt->LastExecution)){
            $nbt->LastExecution = new LongTag("LastExecution", 0); // son çalıştırma
        }
        if(!isset($nbt->auto)){
            $nbt->auto = new IntTag("auto", 0);
        }

        $this->permission = new PermissibleBase($this);

        $this->scheduleUpdate();
    }

    /**
     * @param string $str
     */
    public function setName(string $str){
        $this->namedtag->CustomName = new StringTag("CustomName", $str);
    }

    /**
     * @return bool
     */
    public function hasName() : bool{
        return isset($this->namedtag->CustomName);
    }

    public function getName() : string{
        return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "CommandBlock";
    }

    public function getCommand() : string{
        return isset($this->namedtag->Command) ? $this->namedtag->Command->getValue() : "";
    }

    public function setCommand(string $command){
        $this->namedtag->Command = new StringTag("Command", $command);
    }

    public function getSuccessCount(){
        return isset($this->namedtag->SuccessCount) ? $this->namedtag->SuccessCount->getValue() : "";
    }

    public function runCommand(){
        $this->server->dispatchCommand($this, $this->getCommand());
    }

    public function getSpawnCompound(){
        $nbt = new CompoundTag("", [
            new StringTag("id", Tile::COMMAND_BLOCK),
            new IntTag("x", (int) $this->x),
            new IntTag("y", (int) $this->y),
            new IntTag("z", (int) $this->z),
            new StringTag("Command", $this->getCommand()),
            new StringTag("blockType", $this->getBlockType()),
            new StringTag("LastOutput", $this->getLastOutput()),
            new ByteTag("TrackOutput", $this->getTrackOutput()),
            new IntTag("SuccessCount", $this->getSuccessCount()),
            new ByteTag("auto", $this->getAuto()),
            new ByteTag("powered", $this->getPowered()),
            new ByteTag("conditionalMode", $this->isConditional()),
        ]);
        return $nbt;
    }

    public function isNormal(){
        return $this->getBlockType() == self::NORMAL;
    }

    public function isRepeating(){
        return $this->getBlockType() == self::REPEATING;
    }

    public function isChain(){
        return $this->getBlockType() == self::CHAIN;
    }

    public function getBlockType(){
        return isset($this->namedtag->blockType) ? $this->namedtag->blockType->getValue() : self::NORMAL;
    }

    public function setBlockType(int $blockType){
        // TODO hata verme ve setBlock
        return $this->namedtag->blockType = new IntTag("blockType", $blockType > 2 or $blockType < 0 ? self::NORMAL : $blockType);
    }

    public function isConditional() : bool{
        return boolval(isset($this->namedtag->conditionalMode) ? $this->namedtag->conditionalMode->getValue() : 0);
    }

    public function getPowered() : bool{
        return boolval(isset($this->namedtag->powered) ? $this->namedtag->powered->getValue() : 0);
    }

    public function getAuto() : bool{
        return boolval(isset($this->namedtag->auto) ? $this->namedtag->auto->getValue() : 0);
    }

    public function setConditional(bool $condition){
        $this->namedtag->conditionMet = new IntTag("conditionMet", +$condition);
    }

    public function setPowered(bool $powered){
        if ($this->getPowered() == $powered) {
            return;
        }
        $this->namedtag->powered = new IntTag("powered", +$powered);
        if ($this->isNormal() && $powered && !$this->getAuto()) {
            $this->runCommand();
        }
    }

    public function setAuto(bool $auto){
        $this->namedtag->auto = new IntTag("auto", +$auto);
    }

    public function setLastOutput(string $lastOutput){
        $this->namedtag->LastOutput = new StringTag("LastOutput", $lastOutput);
    }

    public function getTrackOutput() : bool{
        return boolval(isset($this->namedtag->TrackOutput) ? $this->namedtag->TrackOutput->getValue() : 0);
    }

    public function setTrackOutput(bool $trackOutput) {
        return $this->namedtag->TrackOutput = new IntTag("TrackOutput", $trackOutput);
    }

    public function getLastOutput() : string{
        return isset($this->namedtag->LastOutput) ? $this->namedtag->LastOutput->getValue() : "";
    }

    public function show(Player $player){
        $pk = new ContainerOpenPacket();
    	$pk->type = WindowTypes::COMMAND_BLOCK;
    	$pk->windowId = 64;
    	$pk->x = $this->getFloorX();
    	$pk->y = $this->getFloorY();
    	$pk->z = $this->getFloorZ();
    	$player->dataPacket($pk);
    }

    public function chainUpdate(){
        if ($this->getAuto() or $this->getPowered()) {
            $this->runCommand();
        }
    }

    public function onUpdate(){
        if ($this->closed) {
            return false;
        }
        if (!$this->isRepeating()) {
            return true;
        }
        $this->chainUpdate();
        return true;
    }

    /**
     * @param string $message
     */
    public function sendMessage($message){
        $this->setLastOutput($message);
    }

    /**
     * @return \pocketmine\Server
     */
    public function getServer(){
        return Server::getInstance();
    }

    /**
     * Checks if this instance has a permission overridden
     *
     * @param string|Permission $name
     *
     * @return bool
     */
    public function isPermissionSet($name){
        return $this->permission->isPermissionSet($name);
    }

    /**
     * Returns the permission value if overridden, or the default value if not
     *
     * @param string|Permission $name
     *
     * @return mixed
     */
    public function hasPermission($name){
        return $this->permission->hasPermission($name);
    }

    /**
     * @param Plugin $plugin
     * @param string $name
     * @param bool $value
     *
     * @return PermissionAttachment
     */
    public function addAttachment(Plugin $plugin, $name = null, $value = null){
        return $this->permission->addAttachment($plugin, $name, $value);
    }

    /**
     * @param PermissionAttachment $attachment
     *
     * @return void
     */
    public function removeAttachment(PermissionAttachment $attachment){
        $this->permission->removeAttachment($attachment);
    }

    /**
     * @return void
     */
    public function recalculatePermissions(){
        $this->permission->recalculatePermissions();
    }

    public function getEffectivePermissions(){
        return $this->permission->getEffectivePermissions();
    }

    /**
     * Checks if the current object has operator permissions
     *
     * @return bool
     */
    public function isOp(){
        return true;
    }

    /**
     * Sets the operator permission for the current object
     *
     * @param bool $value
     *
     * @return void
     */
    public function setOp($value){
    }

    public function getIdByBlockType($type){
        $id = [
            self::NORMAL => Block::COMMAND_BLOCK,
            self::REPEATING => Block::REPEATING_COMMAND_BLOCK,
            self::CHAIN => Block::CHAIN_COMMAND_BLOCK
        ];
        return isset($id[$type]) ? $id[$type] : Block::COMMAND_BLOCK;
    }
}
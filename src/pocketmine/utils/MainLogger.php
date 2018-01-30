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

namespace pocketmine\utils;

use LogLevel;

class MainLogger extends \AttachableThreadedLogger{

    /** @var string */
    protected $logFile;
    /** @var \Threaded */
    protected $logStream;
    /** @var bool */
    protected $shutdown;
    /** @var bool */
    protected $logDebug;
    /** @var MainLogger */
    public static $logger = null;
    /** @var bool */
    private $syncFlush = false;

	/**
	 * @param string $logFile
	 * @param bool   $logDebug
	 *
	 * @throws \RuntimeException
	 */
	public function __construct(string $logFile, bool $logDebug = false){
        parent::__construct();
        if(static::$logger instanceof MainLogger){
            throw new \RuntimeException("MainLogger has been already created");
        }
        touch($logFile);
        $this->logFile = $logFile;
        $this->logDebug = $logDebug;
        $this->logStream = new \Threaded;
        $this->start();
	}

	/**
	 * @return MainLogger
	 */
	public static function getLogger() : MainLogger{
		return static::$logger;
	}

    /**
     * Assigns the MainLogger instance to the {@link MainLogger#logger} static property.
     *
     * WARNING: Because static properties are thread-local, this MUST be called from the body of every Thread if you
     * want the logger to be accessible via {@link MainLogger#getLogger}.
     */
    public function registerStatic(){
        if(static::$logger === null){
            static::$logger = $this;
        }
    }

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function emergency($message, $name = "EMERGENCY"){
		$this->send($message, \LogLevel::EMERGENCY, $name, TextFormat::RED);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function alert($message, $name = "ALERT"){
		$this->send($message, \LogLevel::ALERT, $name, TextFormat::RED);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function critical($message, $name = "CRITICAL"){
		$this->send($message, \LogLevel::CRITICAL, $name, TextFormat::RED);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function error($message, $name = "ERROR"){
		$this->send($message, \LogLevel::ERROR, $name, TextFormat::DARK_RED);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function warning($message, $name = "WARNING"){
		$this->send($message, \LogLevel::WARNING, $name, TextFormat::YELLOW);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function notice($message, $name = "NOTICE"){
		$this->send(TextFormat::BOLD . $message, \LogLevel::NOTICE, $name, TextFormat::AQUA);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function info($message, $name = "INFO"){
		$this->send($message, \LogLevel::INFO, $name, TextFormat::WHITE);
	}

	/**
	 * @param string $message
	 * @param string $name
	 */
	public function debug($message, $name = "DEBUG"){
		if($this->logDebug === false){
			return;
		}
		$this->send($message, \LogLevel::DEBUG, $name, TextFormat::GRAY);
	}

	/**
	 * @param bool $logDebug
	 */
	public function setLogDebug(bool $logDebug){
		$this->logDebug = $logDebug;
	}

	/**
	 * @param \Throwable $e
	 * @param null       $trace
	 */
	public function logException(\Throwable $e, $trace = null){
        if($trace === null){
            $trace = $e->getTrace();
        }
        $errstr = $e->getMessage();
        $errfile = $e->getFile();
        $errno = $e->getCode();
        $errline = $e->getLine();

        $errorConversion = [
            0 => "EXCEPTION",
            E_ERROR => "E_ERROR",
            E_WARNING => "E_WARNING",
            E_PARSE => "E_PARSE",
            E_NOTICE => "E_NOTICE",
            E_CORE_ERROR => "E_CORE_ERROR",
            E_CORE_WARNING => "E_CORE_WARNING",
            E_COMPILE_ERROR => "E_COMPILE_ERROR",
            E_COMPILE_WARNING => "E_COMPILE_WARNING",
            E_USER_ERROR => "E_USER_ERROR",
            E_USER_WARNING => "E_USER_WARNING",
            E_USER_NOTICE => "E_USER_NOTICE",
            E_STRICT => "E_STRICT",
            E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
            E_DEPRECATED => "E_DEPRECATED",
            E_USER_DEPRECATED => "E_USER_DEPRECATED"
        ];
        if($errno === 0){
            $type = LogLevel::CRITICAL;
        }else{
            $type = ($errno === E_ERROR or $errno === E_USER_ERROR) ? LogLevel::ERROR : (($errno === E_USER_WARNING or $errno === E_WARNING) ? LogLevel::WARNING : LogLevel::NOTICE);
        }
        $errno = $errorConversion[$errno] ?? $errno;
        $errstr = preg_replace('/\s+/', ' ', trim($errstr));
        $errfile = \pocketmine\cleanPath($errfile);
        $this->log($type, get_class($e) . ": \"$errstr\" ($errno) in \"$errfile\" at line $errline");
        foreach(\pocketmine\getTrace(0, $trace) as $i => $line){
            $this->debug($line, true);
        }

        $this->syncFlushBuffer();
	}

    /**
     * @param mixed $level
     * @param string $message
     * @param string $name
     */
	public function log($level, $message, $name = ""){
		switch($level){
			case LogLevel::EMERGENCY:
				$this->emergency($message, $name != "" ? "EMERGENCY ".$name : "EMERGENCY");
				break;
			case LogLevel::ALERT:
				$this->alert($message, $name != "" ? "ALERT ".$name : "ALERT");
				break;
			case LogLevel::CRITICAL:
				$this->critical($message,$name != "" ? "CRITICAL ".$name : "CRITICAL");
				break;
			case LogLevel::ERROR:
				$this->error($message, $name != "" ? "ERROR ".$name : "ERROR");
				break;
			case LogLevel::WARNING:
				$this->warning($message, $name != "" ? "WARNING ".$name : "WARNING");
				break;
			case LogLevel::NOTICE:
				$this->notice($message, $name != "" ? "NOTICE ".$name : "NOTICE");
				break;
			case LogLevel::INFO:
				$this->info($message, $name != "" ? "INFO ".$name : "INFO");
				break;
			case LogLevel::DEBUG:
				$this->debug($message, $name != "" ? "DEBUG ".$name : "DEBUG");
				break;
		}
	}

	public function shutdown(){
		$this->shutdown = true;
        $this->notify();
	}

	/**
	 * @param $message
	 * @param $level
	 * @param $prefix
	 * @param $color
	 */
	protected function send($message, $level, $prefix, $color){
        $now = time();

		$message = TextFormat::toANSI(TextFormat::GREEN . date("H:i:s", $now) . TextFormat::RESET . $color . " " . $prefix . " §8> " . $color . $message . TextFormat::RESET);
		$cleanMessage = TextFormat::clean($message);

		if(!Terminal::hasFormattingCodes()){
			echo $cleanMessage . PHP_EOL;
		}else{
			echo $message . PHP_EOL;
		}

        foreach($this->attachments as $attachment){
            $attachment->call($level, $message);
        }

        $this->logStream[] = date("Y-m-d", $now) . " " . $cleanMessage . PHP_EOL;
	}

    public function syncFlushBuffer(){
        $this->syncFlush = true;
        $this->synchronized(function(){
            $this->notify(); //write immediately

            while($this->syncFlush){
                $this->wait(); //block until it's all been written to disk
            }
        });
    }

    /**
     * @param resource $logResource
     */
    private function writeLogStream($logResource){
        while($this->logStream->count() > 0){
            $chunk = $this->logStream->shift();
            fwrite($logResource, $chunk);
        }

        if($this->syncFlush){
            $this->syncFlush = false;
            $this->notify(); //if this was due to a sync flush, tell the caller to stop waiting
        }
    }

	public function run(){
        $this->shutdown = false;
        $logResource = fopen($this->logFile, "ab");
        if(!is_resource($logResource)){
            throw new \RuntimeException("Couldn't open log file");
        }

        while($this->shutdown === false){
            $this->writeLogStream($logResource);
            $this->synchronized(function(){
                $this->wait(25000);
            });
        }

        $this->writeLogStream($logResource);

        fclose($logResource);
	}

    public function directSend($message){
        if(Terminal::hasFormattingCodes()){
            echo TextFormat::toANSI($message) . PHP_EOL;
        }else{
            echo TextFormat::clean($message) . PHP_EOL;
        }
    }
}

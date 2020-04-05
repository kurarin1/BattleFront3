<?php

namespace bf3\provider\providers;

use bf3\BattleFront3;
use pocketmine\utils\Config;

class YamlProvider extends Provider {

    const FILE_NAME = "";
    const DEFAULT_VALUES = [];

    /* @var $config Config*/
    protected $config;

    public function open(){
        $this->config = new Config(BattleFront3::getInstance()->getDataFolder() . static::FILE_NAME . ".yml", Config::YAML, static::DEFAULT_VALUES);
    }

    public function close(){
        $this->config->save();
    }

}
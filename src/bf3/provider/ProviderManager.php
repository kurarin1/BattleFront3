<?php

namespace bf3\provider;

require_once __DIR__ . '/../../../vendor/autoload.php';

use bf3\BattleFront3;
use bf3\provider\providers\DBSettingProvider;
use bf3\provider\providers\Provider;

class ProviderManager{

    /* @var $db \SQLite3*/
    private static $db;
    /* @var $providers Provider[]*/
    private static $providers;

    public static function init(){
        self::register(new DBSettingProvider());
        self::$db = new \SQLite3(BattleFront3::getInstance()->getDataFolder() . "BF3DB.sqlite3");
    }

    public static function close(){
        foreach (self::$providers as $provider) $provider->close();
    }

    public static function register(Provider $provider){
        self::$providers[$provider->getId()] = $provider;
        $provider->open();
    }

    public static function get(string $id) : Provider{
        return self::$providers[$id];
    }

}
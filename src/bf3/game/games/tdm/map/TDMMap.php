<?php

namespace bf3\game\games\tdm\map;

use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\Server;
use pocketmine\utils\Color;

abstract class TDMMap
{
    const MAP_ID = "";
    const MAP_NAME = "";
    const LEVEL_NAME = "";

    const LOCK_TIME = false;
    const TIME = 0;

    const SPAWN_LOCATION = [
        0 => [0, 0, 0, 0, 0],//x,y,z,yaw,pitch
        1 => [0, 0, 0, 0, 0]
    ];

    const TEAM_NAME = [
        0 => "Red",
        1 => "Blue"
    ];

    const TEAM_COLORCODE =[
        0 => "§c",
        1 => "§9"
    ];

    const TEAM_RGB = [
        0 => [255, 0, 0],
        1 => [0, 0, 255]
    ];

    /* @var $level Level*/
    private $level;

    public function __construct(){
        Server::getInstance()->loadLevel(static::LEVEL_NAME);
        $this->level = Server::getInstance()->getLevelByName(static::LEVEL_NAME);
        if(static::LOCK_TIME) $this->level->stopTime();
        $this->level->setTime(static::TIME);
    }

    public function getSpawnLocation(int $team) : Location{
        $data = static::SPAWN_LOCATION[$team];
        return new Location($data[0], $data[1], $data[2], $data[3], $data[4], $this->level);
    }

    public function getTeamName(int $team) : string {
        return static::TEAM_NAME[$team];
    }

    public function getTeamColorCode(int $team) : string {
        return static::TEAM_COLORCODE[$team];
    }

    public function getColoredTeamName(int $team) : string {
        return $this->getTeamColorCode($team) . $this->getTeamName($team) . "§r§f";
    }

    public function getTeamRGB(int $team) : Color{
        $data = static::TEAM_RGB[$team];
        return new Color($data[0], $data[1], $data[2]);
    }

    public function getMapName() : string {
        return static::MAP_NAME;
    }

    public function getLevel(){
        return $this->level;
    }

    public function close(){
        Server::getInstance()->unloadLevel($this->level);
    }


}
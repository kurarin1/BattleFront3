<?php

namespace bf3\game\games\tdm;

use bf3\BattleFront3;
use bf3\game\GameManager;
use bf3\game\games\Game;
use bf3\game\games\tdm\map\TDMHightower;
use bf3\game\games\tdm\map\TDMMap;
use bfguns\BFGuns;
use dummyapi\dummy\Dummy;
use dummyapi\DummyAPI;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\item\LeatherCap;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\UUID;

class TeamDeathMatch extends Game
{

    const GAME_ID = "tdm";

    const EVENT_LISTENER = TDMEventHandler::class;

    const MIN_PLAYER = 1;

    const HP = 100;

    /* @var $maps string[]*/
    private static $maps = [
        TDMHightower::MAP_ID => TDMHightower::class
    ];

    /* @var $TimeTableStatus int*/
    private $TimeTableStatus = -1;//試合の進行状況
    /* @var $map TDMMap*/
    private $map;
    /* @var $teamMembers array[]*/
    private $teamMembers = [
        0 => [
            //string name => Player player
        ],
        1 => []
    ];
    /* @var $teamIndex int*/
    private $teamIndex = [
        //string name => int team
    ];
    /* @var $killPoint int[]*/
    private $killPoint = [
        0 => 0,
        1 => 0
    ];

    public function init(){
        $this->map = new self::$maps[array_rand(self::$maps)]();//マップ選択
        parent::init();
        $this->TimeTable();
    }

    public function fin(){

    }

    public function TimeTable(){
        $this->TimeTableStatus++;

        switch($this->TimeTableStatus){

            case 0:
                break;

        }
    }

    public function join(Player $player){
        if(isset($this->teamIndex[$player->getName()])){//再参加
            $team = $this->teamIndex[$player->getName()];
        }
        else{//新規参加
            $team = (count($this->teamMembers[0]) <= count($this->teamMembers[1])) ? 0 : 1;
            $this->teamIndex[$player->getName()] = $team;
        }
        $this->teamMembers[$team][$player->getName()] = $player;

        $player->setGamemode(Player::SURVIVAL);
        $player->teleport($this->map->getSpawnLocation($team));
        $player->setSpawn($this->map->getSpawnLocation($team));
        $player->setMaxHealth(self::HP);
        $player->setHealth(self::HP);
        $player->setNameTag($this->map->getTeamColorCode($team) . $player->getName() . "§r§f");
        $player->setDisplayName($this->map->getTeamColorCode($team) . $player->getName() . "§r§f");
        $player->getArmorInventory()->setContents([]);
        $player->getInventory()->setContents([]);

        $cap = new LeatherCap();
        $cap->setCustomColor($this->map->getTeamRGB($team));
        $player->getArmorInventory()->setHelmet($cap);

        $gun = BFGuns::getWeaponManager()->getWeapon("Effexor")->getItem();
        (new PlayerItemHeldEvent($player, $gun, 0))->call();
        $player->getInventory()->addItem($gun);

        $player->sendMessage("§l§aGAME>>§r§fあなたは" . $this->map->getColoredTeamName($team) . "チームです、敵を殲滅してください");
    }

    public function addKillPoint(int $team, int $amount = 1){
        $this->killPoint[$team] += $amount;
    }

    public function getTeam(Player $player){
        return $this->teamIndex[$player->getName()];
    }
}
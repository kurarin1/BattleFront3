<?php

namespace bf3\game\games\tdm;

use bf3\BattleFront3;
use bf3\game\GameManager;
use bf3\game\games\Game;
use bf3\game\games\tdm\map\TDMHightower;
use bf3\game\games\tdm\map\TDMMap;
use bf3\game\games\tdm\task\TDMGameTask;
use bf3\game\games\tdm\task\TDMResultTask;
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

    const TIME = 10;//10分

    const MAX_KILL = 1;

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
        $this->map->getLevel()->setAutoSave(false);
        parent::init();
        $this->TimeTable();
    }

    public function fin(){
        foreach ($this->players as $player){
            unset($this->players[$player->getName()]);
            BattleFront3::getInstance()->gotoHub($player);
            BattleFront3::getInstance()->setHubHealth($player);
            BattleFront3::getInstance()->setHubInventory($player);
            BattleFront3::getInstance()->setHubSpawn($player);
        }
        $this->map->close();
    }

    public function TimeTable(){
        $this->TimeTableStatus++;

        switch($this->TimeTableStatus){

            case 0:
                $this->broadcastTitle("§l§cGame Start!!§r", $this->map->getMapName(), 5, 20, 20);
                $this->broadcastSound("random.totem");
                BattleFront3::getInstance()->getScheduler()->scheduleRepeatingTask(new TDMGameTask($this), 20);
                break;

            case 1:
                $this->broadcastTitle("§l§cGame Set!!§r", "§f試合終了!!", 5, 20, 10);
                $this->broadcastSound("random.totem", 1.5);
                BattleFront3::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new TDMResultTask($this), 30, 20);
                break;

            case 2:
                $this->close();
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
            $this->players[$player->getName()] = $player;
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
        if($this->killPoint[$team] >= self::MAX_KILL && $this->TimeTableStatus === 0){
            $this->TimeTable();
        }
    }

    public function getKillPoint(int $team){
        return $this->killPoint[$team];
    }

    public function getTeam(Player $player){
        return $this->teamIndex[$player->getName()];
    }

    public function getTimeTableStatus(){
        return $this->TimeTableStatus;
    }

    public function getMap() : TDMMap{
        return $this->map;
    }

    /* @return Player[]*/
    public function getTeamPlayers(int $team) : array {
        return $this->teamMembers[$team];
    }
}
<?php

namespace bf3\game\games\tdm;

use bf3\BattleFront3;
use bf3\game\GameManager;
use bf3\game\games\Game;
use bf3\game\games\tdm\bossbar\TDMBossBar;
use bf3\game\games\tdm\map\TDMHightower;
use bf3\game\games\tdm\map\TDMKingsRow;
use bf3\game\games\tdm\map\TDMMap;
use bf3\game\games\tdm\scoreboard\TDMScoreboard;
use bf3\game\games\tdm\task\TDMGameTask;
use bf3\game\games\tdm\task\TDMResultTask;
use bf3\game\games\tdm\task\TDMTipTask;
use bf3\utils\Discord;
use bfguns\BFGuns;
use bossbarapi\bossbar\BossBar;
use bossbarapi\BossBarAPI;
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
use scoreboardapi\scoreboard\Scoreboard;
use scoreboardapi\ScoreboardAPI;

class TeamDeathMatch extends Game
{

    const GAME_ID = "tdm";

    const EVENT_LISTENER = TDMEventHandler::class;

    const MIN_PLAYER = 1;

    const HP = 40;

    const TIME = 60 * 19;

    const MAX_KILL = 10;

    /* @var $maps string[]*/
    private static $maps = [
        TDMHightower::MAP_ID => TDMHightower::class,
        TDMKingsRow::MAP_ID => TDMKingsRow::class,
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
    /* @var $archieves array[]*/
    private $archieves = [
        //name => ["kill" => int, "death" => int, "streak" => int]
    ];

    public function init(){
        $this->map = new self::$maps[array_rand(self::$maps)]();//マップ選択
        $this->map->getLevel()->setAutoSave(false);
        parent::init();
        BattleFront3::getInstance()->getScheduler()->scheduleRepeatingTask(new TDMTipTask($this), 20);
        $this->TimeTable();
        Discord::sendMessage(Discord::WEBHOOK_GAMEINFO, "BattleFront3", '**❗`' . $this->getName() . '`が開始されました ステージ：`' . $this->map->getMapName() . '` **(' . date("m/d H:i") . ')');
    }

    public function fin(){
        foreach ($this->players as $player){
            unset($this->players[$player->getName()]);
            $player->removeAllEffects();
            $player->sendTip("  ");//ステータス表示の削除
            BattleFront3::getInstance()->setHubBossBar($player);
            BattleFront3::getInstance()->setHubScoreboard($player);
            BattleFront3::getInstance()->gotoHub($player);
            BattleFront3::getInstance()->setHubHealth($player);
            BattleFront3::getInstance()->setHubFood($player);
            BattleFront3::getInstance()->setHubInventory($player);
            BattleFront3::getInstance()->setHubNameTags($player);
            BattleFront3::getInstance()->setHubSpawn($player);
        }
        $this->map->close();
        Discord::sendMessage(Discord::WEBHOOK_GAMEINFO, "BattleFront3", '**❗`' . $this->getName() . '`が終了しました **(' . date("m/d H:i") . ')');
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
                foreach ($this->getAllBossBar() as $bossBar){
                    $bossBar->setTitle("§l§3Game>>§f チームデスマッチ §3Stage>>§f " . $this->getMap()->getMapName() . " §3Time>>§f 試合終了");
                    $bossBar->setPercentage(0);
                }
                BattleFront3::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new TDMResultTask($this), 50, 20);
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
            $this->archieves[$player->getName()] = ["kill" => 0, "death" => 0, "streak" => 0];
        }
        $this->teamMembers[$team][$player->getName()] = $player;

        TDMBossBar::create($player);
        TDMScoreboard::create($player);

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

    public function quit(Player $player){
        parent::quit($player);
    }

    public function addKillPoint(int $team, int $amount = 1){
        $this->killPoint[$team] += $amount;
        foreach ($this->players as $player){
            $scoreboard = ScoreboardAPI::getInstance()->getScoreboard($player);
            if($scoreboard instanceof TDMScoreboard){
                $scoreboard->updateScore();
            }
        }
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

    public function addKill(Player $player){
        $this->archieves[$player->getName()]["kill"]++;
        $this->archieves[$player->getName()]["streak"]++;

        if($this->archieves[$player->getName()]["streak"] >= 3){
            $this->broadcastMessage("§l>>§r" . $player->getDisplayName() . "§rが" . $this->archieves[$player->getName()]["streak"] . "キルストリークを達成");
            Discord::sendMessage(Discord::WEBHOOK_GAMEINFO, "BattleFront3", '**❗❗' . $player->getName() . 'が' . $this->archieves[$player->getName()]["streak"] . 'キルストリークを達成しました**');
        }
    }

    public function getKill(Player $player) : int{
        return $this->archieves[$player->getName()]["kill"];
    }

    public function addDeath(Player $player){
        $this->archieves[$player->getName()]["death"]++;
    }

    public function  resetKillStreak(Player $player, ?Player $killer = null){
        if($killer instanceof Player){
            if($this->archieves[$player->getName()]["streak"] >= 3){
                $this->broadcastMessage("§l>>§r" . $killer->getDisplayName() . "§rが" . $player->getDisplayName() . "§rの" . $this->archieves[$player->getName()]["streak"] . "キルストリークを阻止");
                Discord::sendMessage(Discord::WEBHOOK_GAMEINFO, "BattleFront3", '**❗❗' . $killer->getName() . "§fが" . $player->getName() . "§fの" . $this->archieves[$player->getName()]["streak"] . 'キルストリークを阻止しました**');
            }
        }

        $this->archieves[$player->getName()]["streak"] = 0;
    }

    public function getDeath(Player $player) : int{
        return $this->archieves[$player->getName()]["death"];
    }

    public function getStreak(Player $player) : int{
        return $this->archieves[$player->getName()]["streak"];
    }

    /* @return Player[]*/
    public function getTeamPlayers(int $team) : array {
        return $this->teamMembers[$team];
    }

    /* @return TDMBossBar[]*/
    public function getAllBossBar() : array {
        $bars = [];
        foreach ($this->players as $player){
            $bossbar = BossBarAPI::getInstance()->getBossBar($player);
            if($bossbar instanceof TDMBossBar) $bars[] = $bossbar;
        }

        return $bars;
    }
}
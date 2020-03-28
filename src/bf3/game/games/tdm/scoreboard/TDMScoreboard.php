<?php

namespace bf3\game\games\tdm\scoreboard;

use bf3\game\GameManager;
use bf3\game\games\tdm\TeamDeathMatch;
use pocketmine\Player;
use scoreboardapi\scoreboard\Scoreboard;

class TDMScoreboard extends Scoreboard
{

    public function __construct(Player $player){
        parent::__construct($player);
        /* @var $tdm TeamDeathMatch*/
        $tdm = GameManager::getGame();
        $this->setDisplayName("BattleFront§c3§f");
        $this->setLine(0,"     §3v0.1.0    ");
        $this->setLine(2,"§7-----------");
        $this->setLine(3,"目標: 敵を殲滅せよ");
        $this->setLine(4,"§7----------- ");
        $this->setLine(5, "TEAM : §f" . $tdm->getMap()->getColoredTeamName($tdm->getTeam($player)));
        $this->updateScore();
    }

    public function updateScore(){
        /* @var $tdm TeamDeathMatch*/
        $tdm = GameManager::getGame();
        $this->setLine(6, str_pad($tdm->getMap()->getColoredTeamName(0), 15, " ") . ": " . $tdm->getKillPoint(0) . "/" . TeamDeathMatch::MAX_KILL);
        $this->setLine(7, str_pad($tdm->getMap()->getColoredTeamName(1), 15, " ") . ": " . $tdm->getKillPoint(1) . "/" . TeamDeathMatch::MAX_KILL);
    }

}
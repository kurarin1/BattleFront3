<?php

namespace bf3\game\games\tdm\task;

use bf3\game\GameManager;
use bf3\game\games\tdm\TeamDeathMatch;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class TDMResultTask extends TDMTask
{

    private $phase = -1;

    public function onRun(int $currentTick){
        $this->phase++;
        switch($this->phase){

            case 1:
                $winTeam = $this->game->getKillPoint(0) >= $this->game->getKillPoint(1) ? 0 : 1;
                $this->game->broadcastSound("random.totem");
                Server::getInstance()->broadcastTitle("§l§cYOU WIN", "勝利", 0, 20, 10, $this->game->getTeamPlayers($winTeam));
                Server::getInstance()->broadcastTitle("§l§9YOU LOSE...", "敗北...", 0, 20, 10, $this->game->getTeamPlayers(abs($winTeam - 1)));
                break;

            case 5:
                $this->game->TimeTable();
                $this->getHandler()->cancel();
                break;

        }
    }
}
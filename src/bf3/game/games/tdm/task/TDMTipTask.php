<?php

namespace bf3\game\games\tdm\task;

use bf3\game\GameManager;
use bf3\game\games\tdm\bossbar\TDMBossBar;
use bf3\game\games\tdm\TeamDeathMatch;
use bossbarapi\BossBarAPI;
use ddapi\DeviceDataAPI;
use pocketmine\scheduler\Task;

class TDMTipTask extends TDMTask
{

    public function onRun(int $currentTick){
        if($this->game->isClosed()){
            $this->getHandler()->cancel();
        }

        foreach ($this->game->getPlayers() as $player){
            $player->sendTip((DeviceDataAPI::getInstance()->getDeviceOS($player) === DeviceDataAPI::OS_WINDOWS ? "" : "\n\n\n\n\n") . "§l｜ §4KILL:§f " . $this->game->getKill($player) . " §4DEATH: §f" . $this->game->getDeath($player) . " §4STREAK: §f" . $this->game->getStreak($player) . " ｜");
        }
    }
}
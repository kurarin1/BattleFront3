<?php

namespace bf3\game\games\tdm\task;

use bf3\game\GameManager;
use bf3\game\games\tdm\TeamDeathMatch;
use BlockHorizons\Fireworks\entity\FireworksRocket;
use BlockHorizons\Fireworks\item\Fireworks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class TDMResultTask extends TDMTask
{

    const FIREWORKS_TIME = 8;

    private $phase = 0;

    public function onRun(int $currentTick){
        $this->phase++;
        switch($this->phase){

            case 1:
                $winTeam = $this->game->getKillPoint(0) >= $this->game->getKillPoint(1) ? 0 : 1;
                $this->game->broadcastSound("random.totem");
                Server::getInstance()->broadcastTitle("§l§cYOU WIN", "勝利", 0, 25, 10, $this->game->getTeamPlayers($winTeam));
                Server::getInstance()->broadcastTitle("§l§9YOU LOSE...", "敗北...", 0, 20, 15, $this->game->getTeamPlayers(abs($winTeam - 1)));
                break;

            default://装飾
                foreach ($this->game->getAllBossBar() as $bossBar){
                    $remain = self::FIREWORKS_TIME - $this->phase + 1;
                    $bossBar->setTitle("§lあと" . $remain . "秒でロビーへ戻ります");
                    $bossBar->setPercentage($remain / self::FIREWORKS_TIME);
                }
                $fwType = [Fireworks::TYPE_SMALL_SPHERE, Fireworks::TYPE_HUGE_SPHERE, Fireworks::TYPE_STAR, Fireworks::TYPE_CREEPER_HEAD, Fireworks::TYPE_BURST];
                $fwColor = [Fireworks::COLOR_RED, Fireworks::COLOR_BLUE, Fireworks::COLOR_GRAY, Fireworks::COLOR_PINK, Fireworks::COLOR_GREEN, Fireworks::COLOR_YELLOW, Fireworks::COLOR_LIGHT_AQUA, Fireworks::COLOR_GOLD, Fireworks::COLOR_WHITE];
                foreach ($this->game->getPlayers() as $player){
                    for($c=0; $c < mt_rand(1, 4); $c++){
                        $fw = new Fireworks();
                        for($i=0; $i < mt_rand(2, 6)/*0にするとクライアントが落ちます*/; $i++) $fw->addExplosion($fwType[array_rand($fwType)], $fwColor[array_rand($fwColor)], mt_rand(0, 5) === 0 ? $fwColor[array_rand($fwColor)] : "", mt_rand(0, 8) === 0, mt_rand(0, 3) === 0);
                        $fw->setFlightDuration(mt_rand(20, 30) * 0.1);
                        $nbt = FireworksRocket::createBaseNBT($player->add(mt_rand(-6, 6), mt_rand(5 ,15) * 0.1, mt_rand(-6, 6)), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
                        $entity = FireworksRocket::createEntity("FireworksRocket", $player->getLevel(), $nbt, $fw);
                        if ($entity instanceof FireworksRocket) {
                            $entity->spawnToAll();
                        }
                    }
                }
                break;

            case (self::FIREWORKS_TIME + 1):
                $this->game->TimeTable();
                $this->getHandler()->cancel();
                break;

        }
    }
}
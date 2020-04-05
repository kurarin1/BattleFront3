<?php

namespace bf3\hub;

use bf3\BattleFront3;
use bf3\game\GameManager;
use bf3\utils\Utils;
use bfguns\BFGuns;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class TeleportForm implements Form
{

    private $title;

    public function __construct(string $title = "転位術師"){
        $this->title = $title;
    }

    public function handleResponse(Player $player, $data): void{
        if($data === null) return;

        if(GameManager::isPlayer($player)) return;

        BattleFront3::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
            function(int $currentTick) use($player) : void{
                if($player->isOnline()) Utils::playSoundTo($player, "mob.endermen.portal");
            }
        ), 2);

        switch($data){

            case 0:
                $player->teleport(BattleFront3::getInstance()->getHubLevel()->getSpawnLocation());
                BattleFront3::getInstance()->setHubInventory($player);
                break;

            case 1:
                $player->teleport(new Location(343, 22, 342, 180, 0, BattleFront3::getInstance()->getHubLevel()));
                $gun = BFGuns::getWeaponManager()->getWeapon("AR01")->getItem();
                (new PlayerItemHeldEvent($player, $gun, 0))->call();
                $player->getInventory()->addItem($gun);
                $player->getInventory()->addItem(BFGuns::getWeaponManager()->getWeapon("SMG01")->getItem());
                $player->getInventory()->addItem(BFGuns::getWeaponManager()->getWeapon("SG01")->getItem());
                $player->getInventory()->addItem(BFGuns::getWeaponManager()->getWeapon("SR01")->getItem());
                $player->getInventory()->addItem(BFGuns::getWeaponManager()->getWeapon("HG01")->getItem());
                break;

        }
    }

    public function jsonSerialize(){
        return [
            'type' => 'form',
            'title' => $this->title,
            'content' => '転移する先を選択してください',
            'buttons' => [
                [
                    'text' => "§lHub§r§8\nロビー"
                ],
                [
                    'text' => "§lFiringRange§r§8\n射撃練習場"
                ]
            ]
        ];
    }
}
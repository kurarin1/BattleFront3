<?php

namespace bf3\npc;

use bf3\game\form\GameReceptionForm;
use bf3\hub\TeleportForm;
use bf3\resource\ResourceReader;
use dummyapi\dummy\Dummy;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\utils\UUID;

class Teleporter extends Dummy
{

    public function __construct(Location $location){
        parent::__construct(UUID::fromRandom(), $location, ResourceReader::generateSkin("teleporter.png", "normal.json", "geometry.humanoid.customSlim"), "転位術師");
    }

    public function onTouch(Player $player){
        $player->sendForm(new TeleportForm());
    }

}
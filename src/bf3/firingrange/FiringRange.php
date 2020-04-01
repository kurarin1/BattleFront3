<?php

namespace bf3\firingrange;

use bf3\BattleFront3;
use bf3\firingrange\entity\TargetV1;
use bf3\npc\Teleporter;
use pocketmine\entity\Entity;
use pocketmine\level\Location;
use pocketmine\math\Vector3;

class FiringRange{

    public static function init(){
        Entity::registerEntity(TargetV1::class, true);
        for($i=0; $i<7; $i++){
            (new TargetV1(BattleFront3::getInstance()->getHubLevel(), Entity::createBaseNBT(new Vector3(336.5 + $i*2, 21, 324.5 - $i*10), new Vector3(0, 0, 0), 0, 0)))->spawnToAll();
        }
        Teleporter::create(new Location(343.5, 21, 349.5, 180, 0, BattleFront3::getInstance()->getHubLevel()));
    }

}
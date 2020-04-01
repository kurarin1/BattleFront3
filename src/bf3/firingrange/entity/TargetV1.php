<?php

namespace bf3\firingrange\entity;

use bf3\resource\ResourceReader;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class TargetV1 extends Human
{

    private static $skinCache = null;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        if(self::$skinCache === null){
            self::$skinCache = ResourceReader::generateSkin("targetv1.png", "targetv1.json", "geometry.targetvone");
        }
        $this->setSkin(self::$skinCache);
        parent::__construct($level, $nbt);
    }

    public function attack(EntityDamageEvent $source) : void
    {
        $source->call();
        $this->setLastDamageCause($source);

        if($source->getCause() === EntityDamageEvent::CAUSE_MAGIC) $this->kill();
    }

    public function setHealth(float $amount) : void{}

    public function knockBack(Entity $attacker, float $damage, float $x, float $z, float $base = 0.4) : void{}

}
<?php

namespace bf3\game\games\tdm\map;

class TDMHightower extends TDMMap
{

    const MAP_ID = "hightower";
    const MAP_NAME = "HighTower";
    const LEVEL_NAME = "Hightower";

    const LOCK_TIME = true;
    const TIME = 20000;

    const SPAWN_LOCATION = [
        0 => [295, 30.5, 405, 180, 0],//x,y,z,yaw,pitch
        1 => [295, 30.5, 264, 0, 0]
    ];

}
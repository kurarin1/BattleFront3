<?php

namespace bf3\provider\providers;

abstract class Provider{

    const PROVIDER_ID = "";

    public function open(){

    }

    public function close(){

    }

    public function getId(){
        return static::PROVIDER_ID;
    }

}
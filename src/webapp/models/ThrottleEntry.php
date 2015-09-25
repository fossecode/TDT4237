<?php

namespace tdt4237\webapp\models;

class ThrottleEntry 
{

    protected $id;
    protected $ip;

    function __construct($id, $ip)
    {
        $this->id = $id;
        $this->ip = $ip;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setIP($ip) {
        $this->ip = $ip;
    }
}

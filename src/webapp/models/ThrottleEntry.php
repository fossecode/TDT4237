<?php

namespace tdt4237\webapp\models;

class ThrottleEntry 
{
    protected $ip;
    protected $userId;
    protected $timestamp;

    function __construct($userId, $ip, $timestamp = false)
    {
        $this->userId    = intval($userId);
        $this->ip        = $ip;
        $this->timestamp = ($timestamp ? $timestamp : date('Y-m-d H:i:s'));
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}

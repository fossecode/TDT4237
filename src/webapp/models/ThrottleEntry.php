<?php

namespace tdt4237\webapp\models;

class ThrottleEntry 
{
    protected $ip;
    protected $authorId;
    protected $timestamp;

    function __construct($authorId, $ip, $timestamp = false)
    {
        $this->authorId  = intval($authorId);
        $this->ip        = $ip;
        $this->timestamp = ($timestamp ? $timestamp : date('Y-m-d H:i:s'));
    }

    public function getAuthorId()
    {
        return $this->authorId;
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

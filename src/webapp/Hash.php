<?php

namespace tdt4237\webapp;

class Hash
{
    public function __construct()
    {
    }

    public static function make($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);

    }

    public function check($password, $hash)
    {
        return password_verify($password, $hash);
    }

}

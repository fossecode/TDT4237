<?php

namespace tdt4237\webapp\models;

class Age
{

    private $age;
    
    public function __construct($age)
    {
        if (! $this->isHuman($age)) {
            throw new \Exception("Age must be between 0 and 130");
        }
        
        $this->age = $age;
    }
    
    public function __toString()
    {
        return $this->age;
    }
    
    private function isHuman($age)
    {
        return is_numeric($age) and $age >= 0 and $age <= 130;
    }
}

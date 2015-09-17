<?php

namespace tdt4237\webapp\models;

class Age
{

    private $age;
    
    public function __construct($age)
    {
        if (! $this->isHuman($age)) {
            throw new \Exception("Age must be inside the 0-130 range");
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

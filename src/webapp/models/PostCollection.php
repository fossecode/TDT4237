<?php

namespace tdt4237\webapp\models;

use ArrayObject;

class PostCollection extends ArrayObject
{

    public function sortByDate()
    {
        $this->uasort(function (Post $a, Post $b) {
            return strcmp($a->getDate(), $b->getDate());
        });
    }
}

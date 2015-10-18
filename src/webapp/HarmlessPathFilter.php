<?php

namespace tdt4237\webapp;

class HarmlessPathFilter {

    private $path;
    private $harmlessExtensions;

    public function __construct ($path) {
        $this->path = $path;
        $this->harmlessExtensions = array (
            'jpg', 'png', 'gif',
            'txt', 'css', 'js'
        );
    }

    public function getRequestPath () {
        return parse_url($this->path)['path'];
    }

    public function getExtensionFromPath () {
        return strtolower(pathinfo($this->getRequestPath(), PATHINFO_EXTENSION));
    }

    public function isHarmless () {
        return in_array($this->getExtensionFromPath(), $this->harmlessExtensions);
    }
}

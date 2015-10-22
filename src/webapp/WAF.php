<?php

namespace tdt4237\webapp;

class Client {
    public $ip;
    public $request;

    public function __construct($ip, $request) {
        $this->ip      = $ip;
        $this->request = $request;
    }
}

class WAF {

    protected $banMessage;
    protected $allowedHTMLTagNames;
    protected $prisoners;
    
    public function __construct ($prisoners = array()) {
        $this->banMessage = $this->getBanMessage();
        $this->prisoners = $prisoners;
        $this->allowedHTMLTagNames = [
            'b', 'strong', 'h1', 'p',
            'h2,', 'h3', 'h4', 'h5'
        ];
    }

    public function getBanMessage() {
        return
            "We have detected malicious activity from this IP address.\n\n"            .
            "As a counter-measure we have decided to ban you from this application.\n" .
            "We appreciate your understanding and cooperation.\n"                      .
            "Best regards, group 10!";
    }

    public function getPrisoners() {
        return $this->prisoners;
    }

    public function throwInJail($client) {
        $this->prisoners[] = $client;
    }

    public function containsSqlInjection($user_input) {
        return (bool) (
            stristr($user_input, '\'--')      || 
            stristr($user_input, '\';--')     ||
            stristr($user_input, 'SLEEP(')    ||
            stristr($user_input, 'SELECT *')  ||
            stristr($user_input, 'UNION ALL') ||
            stristr($user_input, 'OR 1=1')
        );
    }

    public function isIPInJail($ip) {
        foreach ($this->getPrisoners() as $prisoner)
            if ($prisoner->ip == $ip)
                return true;
        return false;
    }

    public function getAllowableTags() {
        $result = "";
        foreach ($this->allowedHTMLTagNames as $allowedHTMLTagName)
            $result = $result . '<' . $allowedHTMLTagName . '>';
        return $result;
    }

    public function containsMaliciousHTMLTags($user_input) {
        $user_input_with_tags_stripped = strip_tags($user_input, $this->getAllowableTags());
        $was_tags_stripped = strlen($user_input_with_tags_stripped) !== strlen($user_input);
        return $was_tags_stripped;
    }

    public function isMalicious($user_input) {
        return $this->containsSqlInjection($user_input) ||
               $this->containsMaliciousHTMLTags($user_input);
    }
}

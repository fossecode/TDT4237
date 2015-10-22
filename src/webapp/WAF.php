<?php

namespace tdt4237\webapp;

class WAF {

    protected $banMessage;
    protected $allowedHTMLTagNames;
    
    public function __construct () {
        $this->banMessage = $this->getBanMessage();
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

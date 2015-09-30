<?php

namespace tdt4237\webapp;
use tdt4237\webapp\repository\RepositoryInterface;
use tdt4237\webapp\repository\ThrottleRepository;
use tdt4237\webapp\models\ThrottleEntry;

use Exception;

class Throttling 
{

    /**
     * @var ThrottleRepository
     */
    private $throttleRepository;

    public function __construct(RepositoryInterface $throttleRepository)
    {
        $this->throttleRepository = $throttleRepository; 
    }

    public function registerEntry($userId, $ip)
    {
        $entry = new ThrottleEntry($userId, $ip);
        $this->throttleRepository->saveNewEntry($entry);
    }

    public function isAttemptToday($throttleEntry) {
        return date('Ymd', strtotime($throttleEntry->getTimestamp())) == date('Ymd');
    }

    public function calculatePenalty($throttleEntriesForAnIP) {
        $attemptsToday = array_filter($throttleEntriesForAnIP, array($this, 'isAttemptToday'));
        $numberOfAttempts = count($attemptsToday);

        if ($numberOfAttempts < 3)
            $secondsPenalty = 0;
        else
            $secondsPenalty = pow($numberOfAttempts, 2);

        return $secondsPenalty; 
    }

    public function delay($ip)
    {
        $throttleEntries = $this->throttleRepository->findAllByIP($ip);
        $penalty = self::calculatePenalty($throttleEntries);
        sleep($penalty);
    }
}

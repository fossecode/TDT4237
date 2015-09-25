<?php

namespace tdt4237\webapp;
use tdt4237\webapp\repository\ThrottleRepository;
use tdt4237\webapp\models\ThrottleEntry;

use Exception;

class Throttling 
{

    /**
     * @var ThrottleRepository
     */
    private $throttleRepository;

    public function __construct(ThrottleRepository $throttleRepository)
    {
        $this->throttleRepository = $throttleRepository; 
    }

    public function registerEntry($authorId, $ip)
    {
        $entry = new ThrottleEntry($authorId, $ip);
        $this->throttleRepository->saveNewEntry($entry);
    }

    public function calculatePenalty($throttleEntries) {
        $secondsPenalty = 0;
        foreach ($throttleEntries as $throttleEntry) {
            $secondsPenalty += 1;
        }
        return $secondsPenalty; 
    }

    public function delay($ip)
    {
        $throttleEntries = $this->throttleRepository->findAllByIP($ip);
        $penalty = self::calculatePenalty($throttleEntries);
        sleep($penalty);
    }
}

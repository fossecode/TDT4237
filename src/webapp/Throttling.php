<?php

namespace tdt4237\webapp;
use tdt4237\webapp\repository\ThrottleRepository;

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

    public function isIPAddressThrottled($ip)
    {
        $throttle = $this->throttleRepository->findByIp($ip);
        return false;
    }
}

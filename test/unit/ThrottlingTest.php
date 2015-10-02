<?php
require dirname('..').'/src/webapp/Throttling.php';
require dirname('..').'/src/webapp/repository/RepositoryInterface.php';

use tdt4237\webapp\Throttling;
use tdt4237\webapp\models\ThrottleEntry;
use tdt4237\webapp\repository\RepositoryInterface;

class ThrottleRepositoryMock implements RepositoryInterface {
    public function find($id)   {}
    public function save($id)   {}
    public function remove($id) {}
}

class ThrottlingTest extends PHPUnit_Framework_TestCase
{

    public function testPenaltyForEnteringWrongPasswordOnceIsZeroSeconds()
    {
        $throttling = new Throttling(new ThrottleRepositoryMock());
        $attempts = array(new ThrottleEntry(1, '88.125.92.124')); 
        $seconds_penalty = $throttling->calculatePenalty($attempts);
        $this->assertEquals(0, $seconds_penalty);
    }

    public function testPenaltyForEnteringWrongPasswordTwiceIsZeroSeconds()
    {
        $throttling = new Throttling(new ThrottleRepositoryMock());
        $attempts = array(
            new ThrottleEntry(1, '88.125.92.124'),
            new ThrottleEntry(1, '88.125.92.124')
        ); 
        $seconds_penalty = $throttling->calculatePenalty($attempts);
        $this->assertEquals(0, $seconds_penalty);
    }

    public function testPenaltyForEnteringWrongPasswordTenTimesIsGreaterThanFiftySeconds()
    {
        $throttling = new Throttling(new ThrottleRepositoryMock());
        $attempts = array();
        for ( $i = 0; $i < 10; $i++)
            array_push($attempts, new ThrottleEntry(1, '88.125.92.124'));
        $seconds_penalty = $throttling->calculatePenalty($attempts);
        $this->assertGreaterThan(50, $seconds_penalty);
    }

    public function testPenaltyForEnteringWrongPasswordIsZeroWhenADayHasPassed()
    {
        $throttling = new Throttling(new ThrottleRepositoryMock());
        $attempts = array(
            # Yesterday
            new ThrottleEntry(1, '88.125.92.124', date('Y-m-d H:i:s', time() - 86400)),
            new ThrottleEntry(1, '88.125.92.124', date('Y-m-d H:i:s', time() - 86300)),
            new ThrottleEntry(1, '88.125.92.124', date('Y-m-d H:i:s', time() - 86200)),
            new ThrottleEntry(1, '88.125.92.124', date('Y-m-d H:i:s', time() - 86100)),
            # Today
            new ThrottleEntry(1, '88.125.92.124', date('Y-m-d H:i:s', time()))
        ); 
        $seconds_penalty = $throttling->calculatePenalty($attempts);
        $this->assertEquals(0, $seconds_penalty);
    }

}

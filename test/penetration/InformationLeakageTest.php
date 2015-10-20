<?php
require_once dirname('..').'/vendor/rmccue/requests/library/Requests.php';
Requests::register_autoloader();

class InformationLeakageTest extends PHPUnit_Framework_TestCase
{
    private $penetration_target;

    protected function setUp()
    {
        global $argv, $argc;
        $this->penetration_target = $argv[2];
    }

    public function testInformationDoesNotLeakOnForgetPassword()
    {
        $request = Requests::get($this->penetration_target . '/forgot/bob');
        $this->assertEquals(strpos($request->body, 'Full name:'), 0, 'Found full name of bob');
        $request = Requests::get($this->penetration_target . '/forgot/bjarni');
        $this->assertEquals(strpos($request->body, 'Full name:'), 0, 'Found full name of bjarni');
        $request = Requests::get($this->penetration_target . '/forgot/admin');
        $this->assertEquals(strpos($request->body, 'Full name:'), 0, 'Found full name of admin');
    }
}

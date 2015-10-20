<?php
require_once dirname('..').'/vendor/rmccue/requests/library/Requests.php';
Requests::register_autoloader();

class DebugFeatureTest extends PHPUnit_Framework_TestCase
{
    private $penetration_target;

    protected function setUp()
    {
        global $argv, $argc;
        $this->penetration_target = $argv[2];
    }

    public function testAllUsersPageIsDisabled()
    {
        $request = Requests::get($this->penetration_target.'/users');
        $this->assertEquals(404, $request->status_code, 'Debug feature where users are listed should return 404.');
    }
}

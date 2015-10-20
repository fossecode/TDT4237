<?php
require_once dirname('..').'/vendor/rmccue/requests/library/Requests.php';
Requests::register_autoloader();


class SQLInjectionTest extends PHPUnit_Framework_TestCase
{
    private $penetration_target;

    protected function setUp()
    {
        global $argv, $argc;
        $this->penetration_target = $argv[2];
    }

    public function testSQLInjectionUnionBasedAttackDoesNotWork()
    {
        $request = Requests::get($this->penetration_target .
            '/forgot/-8110%27%20UNION%20ALL%20SELECT%20NULL%2CNULL' .
            '%2CNULL%2CNULL%2CNULL%2CNULL%2C%28SELECT%20%27qqqbq%2' . 
            '7%7C%7CCOALESCE%28tbl_name%2C%27%20%27%29%7C%7C%27qpz' . 
            'kq%27%20FROM%20sqlite_master%20WHERE%20type%3D%27tabl' .
            'e%27%20LIMIT%203%2C1%29%2CNULL%2CNULL%2CNULL--%20'
        );
        $this->assertEquals(strpos($request->body, 'qqqbqcommentsqpzkq'), 0, 'Found table in response body.');
    }
}

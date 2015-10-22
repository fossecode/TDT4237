<?php
require dirname('.').'/src/webapp/WAF.php';

use tdt4237\webapp\WAF;
use tdt4237\webapp\Client;

class WAFTest extends PHPUnit_Framework_TestCase
{
    public function testWAFReturnsThatSQLInjectionsAreMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("'--"), true);
        $this->assertEquals($waf->isMalicious("';--"), true);
        $this->assertEquals($waf->isMalicious("SLEEP("), true);
        $this->assertEquals($waf->isMalicious("SELECT *"), true);
        $this->assertEquals($waf->isMalicious("UNION ALL"), true);
    }

    public function testWAFReturnsThatOrOneEqualOne()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("OR 1=1"), true);
    }

    public function testWAFDoesNotReturnsThatSingleQuoteMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("'"), false);
    }

    public function testWAFDoesNotReturnThatSingleQuoteDashIsMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("'-"), false);
    }

    public function testWAFReturnsThatScriptTagIsMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("<script>alert(1);</script>"), true);
    }

    public function testWAFReturnsThatScriptTagInUppercaseIsMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("<SCRIPT>alert(1);</SCRIPT>"), true);
    }

    public function testWAFReturnsThatImgTagIsMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("<img src='' />"), true);
    }

    public function testWAFDoesNotReturnThatBoldTagIsMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("<b>im bold</b>"), false);
    }

    public function testWAFDoesNotReturnThatStrongTagIsMalicious()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isMalicious("<strong>im strong</strong>"), false);
    }

    public function testWAFGuardHasNoPrisonersByDefault()
    {
        $waf = new WAF();
        $this->assertEquals(count($waf->getPrisoners()), 0);
    }

    public function testWAFGuardCanTakePrisonersInConstructor()
    {
        $prisoners = [
            new Client('81.234.12.1', '<img>'),
            new Client('81.234.12.1', '<script>')
        ];
        $waf = new WAF($prisoners);
        $this->assertEquals(count($waf->getPrisoners()), 2);
    }

    public function testWAFGuardPutsClientInJailIfMalicious()
    {
        $waf = new WAF();
        $waf->throwInJail(new Client('88.142.212.12', '<script>malicious</script>'));
        $this->assertEquals(count($waf->getPrisoners()), 1);
    }

    public function testWAFIsInJailReturnsTrueIfIPIsInJail()
    {
        $prisoners = [new Client('81.234.12.1', '<img>')];
        $waf = new WAF($prisoners);
        $this->assertEquals($waf->isIPInJail('81.234.12.1'), true);
    }

    public function testWAFIsInJailReturnsFalseIfIPNotInJail()
    {
        $prisoners = [new Client('81.234.12.1', '<img>')];
        $waf = new WAF($prisoners);
        $this->assertEquals($waf->isIPInJail('127.0.0.1'), false);
    }

    public function testWAFIsInJailReturnsFalseIfNoneInJail()
    {
        $waf = new WAF();
        $this->assertEquals($waf->isIPInJail('85.101.1.1'), false);
    }
}

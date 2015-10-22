<?php
require_once dirname('.').'/src/webapp/WAF.php';

use tdt4237\webapp\WAF;

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
}

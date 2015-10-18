<?php
require dirname('.').'/src/webapp/HarmlessPathFilter.php';

use tdt4237\webapp\HarmlessPathFilter;

class HarmlessPathFilterTest extends PHPUnit_Framework_TestCase
{

    public function testShouldReturnFalseIfPathEndsWithDotSql()
    {
        $path = new HarmlessPathFilter('http://website.com/files/database_backup.sql');
        $this->assertEquals($path->isHarmless(), false);
    }

    public function testShouldReturnTrueIfPathEndsWithJPG()
    {
        $path = new HarmlessPathFilter('http://website.com/files/image.jpg');
        $this->assertEquals($path->isHarmless(), true);
    }

    public function testShouldReturnTrueIfPathEndsWithPNG()
    {
        $path = new HarmlessPathFilter('http://website.com/files/image.png');
        $this->assertEquals($path->isHarmless(), true);
    }

    public function testShouldReturnTrueIfPathEndsWithDotSqlDotPNG()
    {
        $path = new HarmlessPathFilter('http://website.com/files/image.sql.png');
        $this->assertEquals($path->isHarmless(), true);
    }

    public function testQueryParametersCanBeStripped()
    {
        $path = new HarmlessPathFilter('http://website.com/files/dump.sql?id=bypass.jpg');
        $this->assertEquals($path->getRequestPath(), '/files/dump.sql');
    }

    public function testQueryParametersCanBeStrippedEvenIfThereAreParametersDefined()
    {
        $path = new HarmlessPathFilter('http://website.com/files/repo.gitignore?this-is-a-querystring');
        $this->assertEquals($path->getRequestPath(), '/files/repo.gitignore');
    }

    public function testFileExtensionCanBeExtractedFromPath()
    {
        $path = new HarmlessPathFilter('/files/file.ext');
        $this->assertEquals($path->getExtensionFromPath(), 'ext');
    }

    public function testFileExtensionAlwaysIsTheLastDotEnding()
    {
        $path = new HarmlessPathFilter('/files/file.with.multiple.dots.jpg');
        $this->assertEquals($path->getExtensionFromPath(), 'jpg');
    }

    public function testFileExtensionIsConvertedToLowerCase()
    {
        $path = new HarmlessPathFilter('/files/file-with-upper-case-extension.TXT');
        $this->assertEquals($path->getExtensionFromPath(), 'txt');
    }

    public function testFileExtensionReturnsFalseIfNoFileExtension()
    {
        $path = new HarmlessPathFilter('/files/file');
        $this->assertEquals($path->getExtensionFromPath(), false);
    }

    public function testShouldReturnFalseIfQueryStringEndsWithDotJPG()
    {
        $path = new HarmlessPathFilter('http://website.com/files/image.sql?id=querystring-ends-with-jpg');
        $this->assertEquals($path->isHarmless(), false);
    }

    public function testShouldReturnTrueIfOnlyHarmlessRequestURIIsProvided()
    {
        $path = new HarmlessPathFilter('/files/image.jpg');
        $this->assertEquals($path->isHarmless(), true);
    }
    
    public function testShouldReturnFalseIfPathLooksLikeADirectory()
    {
        $path = new HarmlessPathFilter('http://website.com/files/directory/');
        $this->assertEquals($path->isHarmless(), false);
    }

    public function testShouldReturnFalseIfPathDoesNotLookLikeAFile()
    {
        $path = new HarmlessPathFilter('http://website.com/files/does-not-look-like-a-file');
        $this->assertEquals($path->isHarmless(), false);
    }
}

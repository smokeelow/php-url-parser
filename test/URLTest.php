<?php

namespace URLParser;

require '../src/URL.php';

class URLTest extends \PHPUnit_Framework_TestCase
{
    public $testUrl = 'http://john:doepass@www.test.mega.sub-domain.another.one.demandware.org.ua:9090/map.s/pl..ace/Chica......go,+IL,+USA/data=!4m2!3m1!1s0x880e2c3cd0f4cbed:0xafe0a6ad09c0c000?sa=X&ved=0CH0Q8gEwEGoVChMI6dSXtYmVyQIVB9UsCh2caAl_#/test/es';

    //without dots in path
    public $parsedUrl = 'http://john:doepass@www.test.mega.sub-domain.another.one.demandware.org.ua:9090/maps/place/Chicago,+IL,+USA/data=!4m2!3m1!1s0x880e2c3cd0f4cbed:0xafe0a6ad09c0c000?sa=X&ved=0CH0Q8gEwEGoVChMI6dSXtYmVyQIVB9UsCh2caAl_#/test/es';

    public $protocol = 'http';
    public $username = 'john';
    public $password = 'doepass';
    public $hostname = 'www.test.mega.sub-domain.another.one.demandware.org.ua';
    public $originalDomain = 'demandware.org.ua';
    public $subDomains = 'test.mega.sub-domain.another.one';
    public $domainZone = 'org.ua';
    public $port = 9090;

    //should be without dots
    public $path = '/maps/place/Chicago,+IL,+USA/data=!4m2!3m1!1s0x880e2c3cd0f4cbed:0xafe0a6ad09c0c000';

    //should be without dots, without slashes at the beginning and in the end
    public $pathForArray = 'maps/place/Chicago,+IL,+USA/data=!4m2!3m1!1s0x880e2c3cd0f4cbed:0xafe0a6ad09c0c000';
    public $query = 'sa=X&ved=0CH0Q8gEwEGoVChMI6dSXtYmVyQIVB9UsCh2caAl_';
    public $hash = '/test/es';

    /**
     * @var \URLParser\URL $url
     */
    public $url;

    /**
     * @covers            \URLParser\URL::__construct
     * @expectedException \Exception
     */
    public function testExeptionIfUrlIsEmpty()
    {
        $url = new URL(null);
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::__toString
     */
    public function testObjectToString()
    {
        $url = new URL($this->testUrl);

        $this->assertInternalType('string', (string)$url);
        $this->assertEquals($this->parsedUrl, (string)$url);
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getPort
     */
    public function testGetPort()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->port, $url->getPort());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getProtocol
     */
    public function testGetProtocol()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->protocol, $url->getProtocol());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getUsername
     */
    public function testGetUsername()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->username, $url->getUsername());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getPassword
     */
    public function testGetPassword()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->password, $url->getPassword());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getPath
     */
    public function testGetPath()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->path, $url->getPath());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getPathAsArray
     */
    public function testGetPathAsArray()
    {
        $url = new URL($this->testUrl);

        $pathArr = explode('/', $this->pathForArray);
        $this->assertEquals($pathArr, $url->getPathAsArray());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getQuery
     */
    public function testGetQuery()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->query, $url->getQuery());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getQueryAsArray
     */
    public function testGetQueryAsArray()
    {
        $url = new URL($this->testUrl);

        parse_str($this->query, $queryArr);
        $this->assertEquals($queryArr, $url->getQueryAsArray());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getHash
     */
    public function testGetHash()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->hash, $url->getHash());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getHostname
     */
    public function testGetHostname()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->hostname, $url->getHostName());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getSubDomains
     */
    public function testGetSubDomains()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->subDomains, $url->getSubDomains());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getSubDomainsAsArray
     */
    public function testGetSubDomainsAsArray()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals(explode('.', $this->subDomains), $url->getSubDomainsAsArray());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getDomainZone
     */
    public function testGetDomainZone()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->domainZone, $url->getDomainZone());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getOriginalDomain
     */
    public function testGetOriginalDomain()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->originalDomain, $url->getOriginalDomain());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getParsedUrl
     */
    public function testGetParsedUrl()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->parsedUrl, $url->getParsedUrl());
    }

    /**
     * @covers \URLParser\URL::__construct
     * @covers \URLParser\URL::getInitUrl
     */
    public function testGetInitUrl()
    {
        $url = new URL($this->testUrl);

        $this->assertEquals($this->testUrl, $url->getInitUrl());
    }
}
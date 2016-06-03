<?php

namespace Adadgio\GearBundle\Tests\Component\Http;

use Adadgio\GearBundle\Component\Http\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testAbsoluteUrl()
    {
        $absolute = 'http://google.com/test/page?var=Albert';

        // test an absolute url
        $isAbsolute = Uri::isAbsolute($absolute);
        $isRelative = Uri::isRelative($absolute);

        $this->assertEquals($isAbsolute, true);
        $this->assertEquals($isRelative, false);
    }

    public function testRelativeUrl()
    {
        $relative = '/test/page?var=Albert';

        // test an absolute url
        $isAbsolute = Uri::isAbsolute($relative);
        $isRelative = Uri::isRelative($relative);

        $this->assertEquals($isAbsolute, false);
        $this->assertEquals($isRelative, true);
    }

    public function testHelpers()
    {
        $url = 'http://google.com/test/page?var=Albert';
        $ssl = 'https://test.com/hi';
        
        $isHttps = Uri::isHttps($ssl);
        $this->assertEquals($isHttps, true);
    }
}

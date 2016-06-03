<?php

namespace Adadgio\GearBundle\Tests\Component\Filesystem;

use Adadgio\GearBundle\Component\Filesystem\Journalizer;

class JournalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testJournalizerCaseNull()
    {
        $journalizer = new Journalizer('/my/basepath/', null);
        $directory = $journalizer->getDir();

        $this->assertEquals($directory, '/my/basepath/dir0-299/0');
    }
    
    public function testJournalizerCase0()
    {
        $journalizer = new Journalizer('/my/basepath/', 0);
        $directory = $journalizer->getDir();

        $this->assertEquals($directory, '/my/basepath/dir0-299/0');
    }

    public function testJournalizerCaseA()
    {
        $journalizer = new Journalizer('/my/basepath/', 54);
        $directory = $journalizer->getDir();

        $this->assertEquals($directory, '/my/basepath/dir0-299/54');
    }

    public function testJournalizerCaseB()
    {
        $journalizer = new Journalizer('my/basepath/', 310);
        $directory = $journalizer->getDir();

        $this->assertEquals($directory, 'my/basepath/dir300-599/310');
    }
}

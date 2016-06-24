<?php

namespace Adadgio\GearBundle\Tests\Component\Reader;

use Adadgio\GearBundle\Component\Reader\Dictionary;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $file = __DIR__.'/dictionary.txt';
        $dictionary = new Dictionary($file);

        $data = $dictionary
            ->read()
            ->getData();

        $this->assertEquals($dictionary->countRows(), 7);
        $this->assertEquals($data['respiratory'], 'lung');
        $this->assertEquals($data['immunogenicity'], 'zoonotic');
    }

}

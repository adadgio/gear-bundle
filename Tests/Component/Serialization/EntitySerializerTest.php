<?php

namespace Adadgio\GearBundle\Tests\Component\Serialization;

use Adadgio\GearBundle\Component\Serialization\EntitySerializer;

class EntitySerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $obj = new Testproduct();
        $obj->setName('This is a test');

        $config = array(
            'testproduct' =>
            array(
                'class'     => 'testproduct',
                'fields'    => array(
                    'name' =>
                    array(
                        'type'      => 'string',
                        'method'    => 'getName',
                        'arg'       => '',
                    ),
                    'id' => array(
                        'type'      => 'int',
                        'method'    => 'getId',
                        'arg'       => '',
                    )
                )
            )
        );

        $serializer = new EntitySerializer($config);
        $this->assertEquals($serializer->serialize($obj), array(
            'name' => 'This is a test',
            'id' => null,
        ));
    }
}

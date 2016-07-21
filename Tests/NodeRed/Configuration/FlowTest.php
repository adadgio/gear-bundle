<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

use Adadgio\GearBundle\NodeRed\Configuration\Flow;

class FlowTest extends \PHPUnit_Framework_TestCase
{
    public function testFlowParsing()
    {
        $flow = new Flow('flow_template.test.json');
        $templatesDir = __DIR__.'/../../Resources';
        $config = array(
            'flows' => array(
                'output' => null, // not needed for this test
                'configuration_class' => null, // not needed for this test
                'parameters' => array('protocol' => 'https://', 'domain' => 'test-domain.com'),
            ),
        );

        $flow
            ->injectConfig(3, $config, $templatesDir)
            ->parseFlow();

        $arrayFlow = $flow->getArray();

        $nodeAId = $arrayFlow[0]['id'];
        $nodeBId = $arrayFlow[1]['id'];
        $nodeCId = $arrayFlow[2]['id'];

        // assert all node ids have changed
        $this->assertNotEquals($nodeAId, 'idA');
        $this->assertNotEquals($nodeBId, 'idB');
        $this->assertNotEquals($nodeCId, 'idC');

        // assert all wires have changed according to the new ids
        $this->assertEquals($arrayFlow[1]['wires'], array(array($nodeAId)));
        $this->assertEquals($arrayFlow[2]['wires'], array( array($nodeCId), array($nodeBId) ));

        // assert variables replacements
        $this->assertEquals($arrayFlow[0]['path'], 'https://test-domain.com/socket/listener/3');
        $this->assertEquals($arrayFlow[1]['url'], 'https://test-domain.com/adadgio/loop/start/3');
    }
}

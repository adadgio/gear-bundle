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
            ->setTab('Ze tab name')
            ->injectConfig(3, $config, $templatesDir)
            ->parseFlow();

        $arrayFlow = $flow->getArray();

        $nodeTab = $arrayFlow[0];
        $nodeAId = $arrayFlow[1]['id'];
        $nodeBId = $arrayFlow[2]['id'];
        $nodeCId = $arrayFlow[3]['id'];

        // test the node has keys specific to a tab
        $this->assertArraySubset(array('label' => 'Ze tab name', 'type' => 'tab'), $nodeTab);

        // assert all nodes int the flow belong to the tab ("z" property)
        $this->assertEquals($arrayFlow[1]['z'], $nodeTab['id']);
        $this->assertEquals($arrayFlow[2]['z'], $nodeTab['id']);
        $this->assertEquals($arrayFlow[3]['z'], $nodeTab['id']);

        // assert all node ids have changed
        $this->assertNotEquals($nodeAId, 'idA');
        $this->assertNotEquals($nodeBId, 'idB');
        $this->assertNotEquals($nodeCId, 'idC');

        // assert all wires have changed according to the new ids
        $this->assertEquals($arrayFlow[2]['wires'], array(array($nodeAId)));
        $this->assertEquals($arrayFlow[3]['wires'], array( array($nodeCId), array($nodeBId) ));
        
        // assert variables replacements
        $this->assertEquals($arrayFlow[1]['path'], 'https://test-domain.com/socket/listener/3');
        $this->assertEquals($arrayFlow[2]['url'], 'https://test-domain.com/adadgio/loop/start/3');

        // assert "x" coordinates displacement
        $this->assertEquals($arrayFlow[2]['x'], (200 + (3*10)));
        $this->assertEquals($arrayFlow[3]['x'], (400 + (3*10)));
    }
}

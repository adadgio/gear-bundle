<?php

namespace Adadgio\GearBundle\Tests\Component\Reflection;

use Adadgio\GearBundle\Component\Reflection\ReflectionAnalysis;

class ReflectionAnalysisTest extends \PHPUnit_Framework_TestCase
{
    public function testReflectionAnalysis()
    {
        $analysis = new ReflectionAnalysis();
        $analysis->of('Adadgio\GearBundle\Tests\Component\Reflection\DummyController::indexAction');

        $existentArgName = $analysis->findTypeHintedArgName('DateTime'); // type hint does exist in controller method
        $nonExistentArgName = $analysis->findTypeHintedArgName('Request'); // type hint does not exist in controller method

        $this->assertEquals($existentArgName, 'datetime');
        $this->assertEquals($nonExistentArgName, false);
    }
    
    public function testReflectionAnalysisErrors()
    {
        $analysis = new ReflectionAnalysis();
        $analysis->of('Adadgio\GearBundle\Tests\Component\Reflection\DummyController');

        $nonExistentArgName = $analysis->findTypeHintedArgName('Request');
        $this->assertEquals($nonExistentArgName, false);
    }
}

<?php

namespace Adadgio\GearBundle\Exception;

class GearException extends \Exception
{
    /**
     * Exception class constructor.
     */
    public function __construct($message = null, $args = array(), $code = 0, \Exception $previous = null)
    {
        // vsprintf arguments can be a strong or an array
        $args = (is_string($args)) ? array($args) : $args;

        // build the exception message accordingly
        $message = vsprintf($message, $args);
        
        // then fallback to classic PHP exceptions
        parent::__construct($message, $code, $previous);
    }
}

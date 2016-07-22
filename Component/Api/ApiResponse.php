<?php

namespace Adadgio\GearBundle\Component\Api;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse extends Response
{
    public function __construct(array $data = array(), $code = 200)
    {
        parent::__construct(json_encode($data), $code);
        
        $this->headers->set('Content-Type', 'application/json; charset=utf-8');
    }
}

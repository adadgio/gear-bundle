<?php

namespace Adadgio\GearBundle\Component\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    public function __construct($data = null, $status = 200, array $headers = array())
    {
        parent::__construct($data, $status, $headers);
    }
}

<?php

namespace Adadgio\GearBundle\Component\Api\Annotation;

/**
 * The annotation definition class for the API.
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Api
{
    /**
     * @var array
     */
    private $properties;

    /**
     * Catch any "api" annotations from a controller class or method(s).
     *
     * @param array Options
     * @return void
     */
    public function __construct($options)
    {
        foreach($options as $key => $value) {
            $this->$key = $this->normalize($key, $value);
        }
    }

    /**
     * Normalize a few parameters to be easier manipulated
     * and still provide flexibility in the annotation.
     *
     * @param  array Raw user annotated properties
     * @return array Normalized properties
     */
    private function normalize($key, $value)
    {
        // method although singular is always an array at the end (but can be null)
        if ($key === 'method' && is_string($value)) {
            return array($value);
        } else {
            return $value;
        }
    }
    
    /**
     * Get properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get property.
     *
     * @return mixed
     */
    public function getProperty($key)
    {
        return isset($this->$key) ? $this->$key : null;
    }

    /**
     * Has property.
     *
     * @return mixed
     */
    public function hasProperty($key)
    {
        return isset($this->$key);
    }
}

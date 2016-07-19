<?php

namespace Adadgio\GearBundle\Component\Serialization;

use Doctrine\Common\Inflector;
use Doctrine\Common\Collections\ArrayCollection;

class EntitySerializer
{
    /**
     * @var array Bundle serialization configuration node.
     */
    private $config;

    /**
     * Service constructor.
     *
     * @param  array Bundle serialization configuration node.
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Serializes on or several objects.
     *
     * @param  mixed An array or ArrayCollection of entities or just one entity
     * @return string JSON encoded serialized entity
     */
    public function serialize($input)
    {
        // input can be an array, an ArrayCollection or one object
        if (is_array($input) OR $input instanceof ArrayCollection) {
            return $this->serializeMany($input);
        } else {
            return $this->serializeOne($input);
        }
    }
    
    /**
     * Serializes many entities.
     *
     * @param  array or ArrayCollection
     * @return JSON serialized content
     */
    public function serializeMany($entities)
    {
        $serialized = array();

        foreach ($entities as $entity) {
            $serialized[] = $this->serializeOne($entity);
        }

        return $serialized;
    }

    /**
     * Serializes one entity.
     *
     * @param  object A doctrine \Entity
     * @return JSON serialized content
     */
    public function serializeOne($entity)
    {
        $serialized = array();
        $class = $this->getEntityName($entity);

        $mapping = $this->getClassMapping(strtolower($class));

        // loop through mapping and use entity getters
        foreach ($mapping['fields'] as $field => $params) {

            if (!empty( $params['method'])) {
                $arg = $params['arg'];
                $get = $params['method'];
                $serialized[$field] = $entity->$get($arg);
            } else {
                $get = 'get'.Inflector\Inflector::classify($field);
                $serialized[$field] = $entity->$get();
            }


        }

        return $serialized;
    }

    /**
     * Get serialization mapping for one class.
     *
     * @param  string Class short name without namespace
     * @return array Class serialization mapping
     */
    private function getClassMapping($class)
    {
        if (!isset($this->config[$class])) {
            throw new \Exception(sprintf('There is no mapping defined for class "%s" in your serialization config', $class));
        }

        return $this->config[$class];
    }

    /**
     * Get entity short class name.
     *
     * @param  object \Entity
     * @return string Short class name
     */
    private function getEntityName($entity)
    {
        $reflection = new \ReflectionClass($entity);
        $class = $reflection->getShortName();

        return $class;
    }
}

<?php

namespace Adadgio\GearBundle\Component\Hydration;

use Adadgio\GearBundle\Exception\GearException;
use Doctrine\Common\Inflector\Inflector;

class EntityHydrator
{
    /**
     * @var array
     */
    protected $mapping;

    /**
     * @var string
     */
    protected $class;

    /**
     * Set entity class namespace.
     *
     * @param  string Entity object full namespace
     * @return object \EntityHydrator
     */
    public function hydrate($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Set data to hydrate entities with
     *
     * @param  string Delimiter (",", ";", "\n", "\t")
     * @return object \EntityHydrator
     */
    public function with(array $data = array())
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Map data indexes with entity fields.
     *
     * @param integer Input array data index
     * @param string Entity field name to map the index with
     * @return object \EntityHydrator
     */
    public function map($index, $property)
    {
        $this->mapping[$index] = 'set'.Inflector::classify($property);

        return $this;
    }

    /**
     * Get hydrated entities.
     *
     * @return array Array of hydrated entities
     */
    public function getEntities()
    {
        $entities = array();
        $class = $this->class;

        foreach ($this->data as $row) {
            $entity = new $class();

            foreach ($row as $index => $value) {
                if (!isset($this->mapping[$index])) {
                    continue;
                }

                // else use the setter to set the entity property
                $set = $this->mapping[$index];
                $entity->$set($value);
                $entities[] = $entity;
            }
        }

        return $entities;
    }
}

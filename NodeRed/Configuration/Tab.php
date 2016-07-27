<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

class Tab
{
    private $id;
    private $label;

    public function __construct($label)
    {
        $this->id = uniqid();
        $this->label = $label;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function toArray()
    {
        array(
            'type'  => 'tab',
            'id'    => $this->id,
            'label' => $this->label,
        );
    }
}

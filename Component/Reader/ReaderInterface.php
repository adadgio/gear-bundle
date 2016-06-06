<?php

namespace Adadgio\GearBundle\Component\Reader;

interface ReaderInterface
{
    /**
     * Makes sur the data is loaded from a file.
     *
     * @param string Input data (from API) or file path.
     */
    public function __construct($input);

    /**
     * Shoud return data as an array
     * @return array Data array
     */
    public function getData();
    
    /**
     * Responsible for iterating through a data source (API, file, CSV, HTML...)
     * This must hydrate $this->data as an array and return \ReaderInterface.
     * @return object Source\Reader
     */
    public function read();
}

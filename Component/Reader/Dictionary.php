<?php

namespace Adadgio\GearBundle\Component\Reader;

use Adadgio\GearBundle\Component\Reader\ReaderInterface;
use Adadgio\GearBundle\Exception\GearException;

class Dictionary implements ReaderInterface
{
    /**
     * @var string CSV fgets delimiter
     */
    protected $delimiter;

    /**
     * @var string Input csv file path
     */
    protected $file;

    /**
     * Final data output
     */
    protected $data = array();

    /**
     * The csv reader constructor, can use raw contents
     * or a file path on local or remote server.
     *
     * @param string Input raw data or file path
     */
    public function __construct($file)
    {
        $this->delimiter = "\n";
        $this->file = $file;
    }

    /**
     * Get data rows count.
     *
     * @return integer
     */
    public function countRows()
    {
        return count($this->data);
    }

    /**
     * Get parsed data.
     *
     * @return array Parsed data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set parsed data (fosr tests or different hydration)
     *
     * @param array Dictionary data
     * @return \Dictionary
     */
    public function setData(array $data = array())
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Reads from the input optionaly with a limit and offset
     *
     * @param  integer Skip a number of rows (if X, x not included in result set)
     * @param  integer Limit number of rows that should be returned
     * @return object \CSv
     */
    public function read()
    {
        $contents = file_get_contents($this->file);
        $data = array_filter(explode($this->delimiter, $contents));
        
        foreach ($data as $row) {
            $parts = explode('=', $row);
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            $this->data[$key] = $val;
        }

        return $this;
    }
}

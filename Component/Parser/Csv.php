<?php

namespace Adadgio\GearBundle\Component\Parser\Csv;

use Adadgio\GearBundle\Exception\GearException;

class Csv
{
    /**
     * Types of input for the constructor
     */
    const RAW_INPUT = null;
    const FILE_INPUT = 'FILE_INPUT';

    /**
     * @var string CSV fgets delimiter
     */
    protected $delimiter;

    /**
     * @var string Input csv file path
     */
    protected $csvfile;

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
    public function __construct($csvfile)
    {
        $this->delimiter = "\t";
        $this->csvfile = $csvfile;
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
     * Set fgetscsv delimiter.
     *
     * @param  string Delimiter (",", ";", "\n", "\t")
     * @return object \Csv
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Reads from the input optionaly with a limit and offset
     */
    public function read($offset = null, $limit = null)
    {
        $handle = fopen($this->csvfile, 'r');
        if (!$handle) { return $this; }

        // create a row counter
        $n = 0;
        $total = 0;
        $counter = 0;

        while (($cells = fgetcsv($handle, 0, $this->delimiter)) !== false) {
            // handle the offset
            if(null !== $offset && $counter < $offset) {
                $counter++;
                continue;
            }

            // handle the limit
            if (null !== $limit && $total > $limit) {
                break;
            }

            // read each line cell
            foreach($cells as $i => $cell) {
                $cell = trim($cell);

                if (true === empty($cell)) {
                    $cell = null;
                }

                $this->data[$n][$i] = $cell;
            }

            $n++;
            $total++;
            $counter++;
        }

        // close file handle
        fclose($handle);

        return $this;
    }
}

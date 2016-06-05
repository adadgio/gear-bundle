<?php

namespace Adadgio\GearBundle\Component\Reader;

use Adadgio\GearBundle\Component\Reader\ReaderInterface;
use Adadgio\GearBundle\Exception\GearException;

class Csv implements ReaderInterface
{
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

        if (is_file($csvfile)) {
            $this->csvfile = $csvfile;
        } else {
            throw new GearException('File not found "%s"', $csvfile);
        }
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
     *
     * @param  integer Skip a number of rows (if X, x not included in result set)
     * @param  integer Limit number of rows that should be returned
     * @return object \CSv
     */
    public function read($skip = null, $limit = null)
    {
        $handle = fopen($this->csvfile, 'r');
        if (!$handle) { return $this; }

        // create a row counter
        $n = 0;
        $total = 0;
        $counter = 0;

        while (($cells = fgetcsv($handle, 0, $this->delimiter)) !== false) {
            // handle the offset
            if(null !== $skip && $counter < $skip) {
                $counter++;
                continue;
            }

            // handle the limit
            if (null !== $limit && $total > ($limit-1)) {
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

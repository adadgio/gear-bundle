<?php

namespace Adadgio\GearBundle\Component\Reader;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * @var object EntityManager
     */
    private $em;

    /**
     *
     */
    private $repository;

    /**
     *
     */
    private $method;

    /**
     *
     */
    private $fields;

    /**
     *
     */
    private $data;

    /**
     *
     */
    private $columns;

    /**
     *
     */
    private $name;

    /**
     *
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     *
     */
    public function from($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     *
     */
    public function with($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     *
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Returns a streamed response.
     */
    public function generate()
    {
        if (null == $this->name) {
            throw new \Exception("You must set a name to the file downloaded with setName() method before generate file.");
        }
        $filename = $this->name;
        $data = $this->data;
        $columns  = $this->columns;
        $response = new StreamedResponse();

        $response->setCallback(function() use ($columns, $data) {
            $handle = fopen('php://output', 'w+');

            // add csv header columns
            fputcsv($handle, $columns,';');

            // add data in the csv
            foreach ($data as $row) {

                $values = array();
                foreach (array_values($row) as $value) {
                    $values[] = $this->toString($value);
                }
                fputcsv($handle, $values, ';');
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type','text/csv; charset=utf-8');
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment;filename="'.$filename.'.csv"');

        return $response;
    }

    /**
     *
     */
    private function toString($value)
    {
        if (is_string($value)) {

            return utf8_decode($value);

        } else if (is_bool($value)) {

            return ($value === true) ? 'TRUE' : 'FALSE';

        } else if (is_numeric($value) OR is_float($value)) {

            return $value;

        } else if (is_array($value)) {

            return implode(';', $value);

        } else if ($value instanceof \Datetime) {

            return $value->format('Y-m-d H:i:s');

        } else if (is_null($value)) {

            return null;

        } else {
            return '[Object Object]';
        }
    }

    /**
     * Get query resulst based on settings.
     */
    public function getDataFromRepository()
    {
        $method = $this->method;

        $this->data = $this->em
            ->getRepository($this->repository)
            ->$method($this->fields);

        return $this;
    }

    /**
     *  set data
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     *  set data
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Finds which header columns are here from the first data row analysis.
     */
    public function setColumnsFromData()
    {
        if (empty($this->data)) {
            $this->columns = array();
        } else {
            $this->columns = array_keys($this->data[0]);
        }

        return $this;
    }

    /**
     * set a file name for the downloaded file from the repository that was choosen.
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}

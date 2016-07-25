<?php

namespace Adadgio\GearBundle\Tests\Component\Reader;

use Adadgio\GearBundle\Component\Reader\CsvReader;

class CsvReaderTest  extends \PHPUnit_Framework_TestCase
{
    public function testCaseLimitOffsetA()
    {
        $file = __DIR__.'/tabs_semicolon.csv';
        $csv = new CsvReader($file);

        $data = $csv
            ->setDelimiter(';')
            ->read(2, 5) // from row 60, get 5 rows
            ->getData();

        $this->assertEquals($csv->countRows(), 5);
        $this->assertEquals($data[0], array('A011', 'Paratyphoede A'));
    }

    public function testCaseLimitOffsetB()
    {
        $file = __DIR__.'/tabs_semicolon.csv';
        $csv = new CsvReader($file);

        $data = $csv
            ->setDelimiter(';')
            ->read(0, 15) // from row 60, get 5 rows
            ->getData();

        $this->assertEquals($csv->countRows(), 15);
        $this->assertEquals($data[0], array('A009', 'Cholera, sans precision'));
    }

    public function testCaseA()
    {
        $file = __DIR__.'/tabs_semicolon.csv';
        $csv = new CsvReader($file);

        $data = $csv
            ->setDelimiter(';')
            ->read()
            ->getData();

        $this->assertEquals($csv->countRows(), 78);
        $this->assertEquals($data[71], array('A520', 'Syphilis cardio-vasculaire'));
    }

    public function testCaseB()
    {
        $file = __DIR__.'/unix_comma.csv';
        $csv = new CsvReader($file);

        $data = $csv
            ->setDelimiter(',')
            ->read()
            ->getData();

        $this->assertEquals($csv->countRows(), 24);
        $this->assertEquals($data[12], array(12, 'Charles', 'Ray', 'crayb@usda.gov', 'Male', '79.202.34.106'));
    }

    public function testCaseC()
    {
        $file = __DIR__.'/unix_semicolon.csv';
        $csv = new CsvReader($file);

        $data = $csv
            ->setDelimiter(';')
            ->read()
            ->getData();

        $this->assertEquals($csv->countRows(), 24);
        $this->assertEquals($data[12], array(12, 'Charles', 'Ray', 'crayb@usda.gov', 'Male', '79.202.34.106'));
    }

    public function testCaseD()
    {
        $file = __DIR__.'/windows_comma.csv';
        $csv = new CsvReader($file);

        $data = $csv
            ->setDelimiter(',')
            ->read()
            ->getData();

        $this->assertEquals($csv->countRows(), 24);
        $this->assertEquals($data[12], array(12, 'Louise', 'Walker', 'lwalkerb@360.cn', 'Female', '144.81.254.156'));
    }
}

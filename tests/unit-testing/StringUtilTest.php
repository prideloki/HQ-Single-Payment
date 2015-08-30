<?php

class Test extends \PHPUnit_Framework_TestCase
{
    private $firstNameFig;
    private $lastNameFig;

    public function setUp()
    {
        parent::setUp();
        $this->firstNameFig = 'John';
        $this->lastNameFig = 'Doe';

    }

    public function test_parse_fullname_with_space()
    {

        list($firstName, $lastName) = App\StringUtil::parseFullName($this->firstNameFig . ' ' . $this->lastNameFig);

        $this->assertEquals($this->firstNameFig, $firstName);
        $this->assertEquals($this->lastNameFig, $lastName);
    }

    public function test_parse_fullname_without_space()
    {
        list($firstName, $lastName) = App\StringUtil::parseFullName($this->firstNameFig . $this->lastNameFig);

        $this->assertEquals($this->firstNameFig.$this->lastNameFig, $firstName);
        $this->assertEquals('', $lastName);
    }

}
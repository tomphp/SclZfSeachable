<?php

namespace SclZfSearchableTests\SearchInfo;

use SclZfSearchable\SearchInfo\BasicSearchInfo;

/**
 * Unit tests from {@see BasicSearchInfo}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class BasicSearchInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The instance to be tested.
     *
     * @var mixed
     */
    protected $searchInfo;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    public function setUp()
    {
        $this->searchInfo = new BasicSearchInfo();
    }

    /**
     * Test getters and setters which don't respond if they have been passed null.
     *
     * @dataProvider getSetProvider
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::setName
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::getName
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::setSearch
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::getSearch
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::setOrderBy
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::getOrderBy
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::setOrder
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::getOrder
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::setCurrentPage
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::getCurrentPage
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::setPageSize
     * @covers       SclZfSearchable\SearchInfo\BasicSearchInfo::getPageSize
     *
     * @return void
     */
    public function testGetSet($name, $value)
    {
        $setter = 'set' . $name;
        $getter = 'get' . $name;

        $this->assertEquals(
            $this->searchInfo,
            $this->searchInfo->$setter($value),
            "$setter didn't return self when a value was passed in."
        );

        $this->assertEquals(
            $value,
            $this->searchInfo->$getter(),
            "$getter returned incorrect value."
        );
    }

    public function getSetProvider()
    {
        return array(
            array('name', 'test-name'),
            array('search', 'search terms'),
            array('orderBy', 'sortcolumn'),
            array('order', 'asc'),
            array('order', 'desc'),
            array('currentPage', 12),
            array('pageSize', 20),
        );
    }

    /**
     * Make sure that when new search terms are set the page number is reset.
     *
     * @return void
     */
    public function testSetSearchResetsCurrentPage()
    {
        $this->searchInfo->setCurrentPage(5);

        $this->searchInfo->setSearch('something');

        $this->assertEquals(1, $this->searchInfo->getCurrentPage());
    }

    /**
     * Test that when setOrder is called with a bad string an exception is thrown.
     *
     * @dataProvider      badColumnNames
     * @covers            SclZfSearchable\SearchInfo\BasicSearchInfo::setOrderBy
     * @expectedException SclZfSearchable\Exception\DomainException
     *
     * @return void
     */
    public function testOrderByWithBadCharacters($badString)
    {
        $this->searchInfo->setOrderBy($badString);
    }

    public function badColumnNames()
    {
        return array(
                array('!'),
                array('"'),
                array('£'),
                array('$'),
                array('%'),
                array('^'),
                array('*'),
                array('('),
                array(')'),
                array('+'),
                array('~'),
                array('#'),
                array('\''),
                array('\\'),
        );
    }

    /**
     * Test that an exception gets thrown when setOrder is called with an illegal value.
     *
     * @covers            SclZfSearchable\SearchInfo\BasicSearchInfo::setOrder
     * @expectedException SclZfSearchable\Exception\DomainException
     *
     * @return void
     */
    public function testSetOrderWithBadValue()
    {
        $this->searchInfo->setOrder('not-desc-or-asc');
    }

    /**
     * testReset
     *
     * @depends testGetSet
     * @covers  SclZfSearchable\SearchInfo\BasicSearchInfo::reset
     *
     * @return void
     */
    public function testReset()
    {
        $this->searchInfo
             ->setSearch('test')
             ->setOrderBy('col')
             ->setOrder('desc')
             ->setCurrentPage(5)
             ->setPageSize(25);

        $this->searchInfo->reset();

        $this->assertNull($this->searchInfo->getSearch(), 'search was not reset');

        $this->assertNull($this->searchInfo->getOrderBy(), 'orderBy was not reset');

        $this->assertEquals('asc', $this->searchInfo->getOrder(), 'order was not reset');

        $this->assertEquals(1, $this->searchInfo->getCurrentPage(), 'currentPage was not reset');

        $this->assertEquals(15, $this->searchInfo->getPageSize(), 'pageSize was not reset');
    }
}

<?php

namespace SclZfSearchableTests\SearchInfo;

use SclZfSearchable\SearchInfo\SessionSearchInfo;

/**
 * Unit tests from {@see SessionSearchInfo}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class SessionSearchInfoTest extends \PHPUnit_Framework_TestCase
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
        $this->searchInfo = new SessionSearchInfo();
    }

    /**
     * Make sure that when new search terms are set the page number is reset.
     *
     * @return void
     */
    public function testSetSearchResetsCurrentPage()
    {
        $this->markTestIncomplete('Fix me');
        return;

        $this->searchInfo->setCurrentPage(5);

        $this->searchInfo->setSearch('something');

        $this->assertEquals(1, $this->searchInfo->getCurrentPage());
    }

    /**
     * Test that when setOrder is called with a bad string an exception is thrown.
     *
     * @dataProvider      badColumnNames
     * @covers            SclZfSearchable\SearchInfo\SessionSearchInfo::setOrderBy
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
     * @covers            SclZfSearchable\SearchInfo\SessionSearchInfo::setOrder
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
     * @covers  SclZfSearchable\SearchInfo\SessionSearchInfo::reset
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

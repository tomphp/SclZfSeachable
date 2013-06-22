<?php

namespace SclZfSearchableTest;

use SclZfSearchable\Searchable;

/**
 * Unit tests for {@see Searchable}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class SearchableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The instance to be tested.
     *
     * @var Searchable
     */
    protected $searchable;

    /**
     * Mock repository
     *
     * @var \SclZfSearchable\SearchableRepositoryInterface
     */
    protected $repository;

    /**
     * Mock SearchInfo object.
     *
     * @var \SclZfSearchable\SearchInfo\SearchInfoInterface
     */
    protected $searchInfo;

    /**
     * Test name for the searchinfo class.
     */
    const SEARCH_INFO_NAME = 'test_search';

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    public function setUp()
    {
        $this->searchable = new Searchable();

        $this->searchInfo = $this->getMock('SclZfSearchable\SearchInfo\SearchInfoInterface');
        $this->searchInfo
             ->expects($this->any())
             ->method('getName')
             ->will($this->returnValue(self::SEARCH_INFO_NAME));
        $this->searchable->setSearchInfo($this->searchInfo);
    }

    /**
     * Test that getSearchInfo and setSearchInfo work correctly.
     *
     * @covers SclZfSearchable\Searchable::getSearchInfo
     * @covers SclZfSearchable\Searchable::setSearchInfo
     *
     * @return void
     */
    public function testGetSearchInfo()
    {
        $this->assertEquals($this->searchInfo, $this->searchable->getSearchInfo());
    }

    /**
     * Test the result from the get param name methods.
     *
     * @dataProvider paramMethodProvider
     * @covers SclZfSearchable\Searchable::searchParamName
     * @covers SclZfSearchable\Searchable::currentPageParamName
     * @covers SclZfSearchable\Searchable::orderByParamName
     * @covers SclZfSearchable\Searchable::orderParamName
     * @covers SclZfSearchable\Searchable::pageSizeParamName
     *
     * @param  string $method   The name of the method to test.
     * @param  string $expected The expected return value.
     * @return void
     */
    public function testGetParamNames($method, $expected)
    {
        $result = $this->searchable->$method();

        $this->assertEquals(
            $expected,
            $result,
            "Expected '$expected' for method $method; got '$result'."
        );
    }

    /**
     * Provides values to test the param name functions.
     */
    public function paramMethodProvider()
    {
        return array(
            array('searchParamName', self::SEARCH_INFO_NAME . '_search'),
            array('currentPageParamName', self::SEARCH_INFO_NAME . '_page'),
            array('orderByParamName', self::SEARCH_INFO_NAME . '_sortcol'),
            array('orderParamName', self::SEARCH_INFO_NAME . '_sortorder'),
            array('pageSizeParamName', self::SEARCH_INFO_NAME . '_pagesize'),
        );
    }

    /**
     * Test that getValues throws an exception is the SearchInfo object hasn't been set.
     *
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException SclZfSearchable\Exception\RuntimeException
     *
     * @return void
     */
    public function testSetValuesWithNoSearchInfo()
    {
        $searchable = new Searchable();

        $request = $this->getMock('Zend\Http\Request');

        $searchable->setValues($request);
    }

    /**
     * Test that getList is called correctly.
     *
     * @covers SclZfSearchable\Searchable::setRepository
     * @covers SclZfSearchable\Searchable::setListName
     * @covers SclZfSearchable\Searchable::getList
     *
     * @return void
     */
    public function testGetList()
    {
    }
}

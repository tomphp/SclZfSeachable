<?php

namespace SclZfSearchableTest;

use SclZfSearchable\Searchable;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

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
            array('orderByParamName', self::SEARCH_INFO_NAME . '_sortcol'),
            array('orderParamName', self::SEARCH_INFO_NAME . '_sortorder'),
            array('currentPageParamName', self::SEARCH_INFO_NAME . '_page'),
            array('pageSizeParamName', self::SEARCH_INFO_NAME . '_pagesize'),
        );
    }

    /**
     * Test that setValues throws an exception is the SearchInfo object hasn't been set.
     *
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException SclZfSearchable\Exception\RuntimeException
     *
     * @return void
     */
    public function testSetValuesWithNoSearchInfo()
    {
        $searchable = new Searchable();

        $searchable->setValues(new Request());
    }

    /**
     * Test that setValues resets the search info when no settings are submitted.
     *
     * @covers SclZfSearchable\Searchable::getList
     *
     * @return void
     */
    public function testSetValuesWithNoValues()
    {
        // No search parameters are set
        $request = new Request();

        $this->searchInfo
             ->expects($this->once())
             ->method('reset');

        $result = $this->searchable->setValues($request);

        $this->assertEquals($this->searchable, $result, 'setValues did not return $this');
    }

    /**
     * Test that setValues sets the appropriate values of the search info object.
     *
     * @covers SclZfSearchable\Searchable::getList
     *
     * @return void
     */
    public function testSetValues()
    {
        // Test values
        $searchTerm  = 'hello world';
        $orderBy     = 'column1';
        $order       = 'asc';
        $currentPage = 3;
        $pageSize    = 20;

        // Prepare the request object to provide the test values
        $request = new Request();

        $request->setPost(new Parameters());
        $request->setQuery(new Parameters());

        $request->getPost()->fromArray(
            array(
                self::SEARCH_INFO_NAME . '_search' => $searchTerm
            )
        );

        $request->getQuery()->fromArray(
            array(
                self::SEARCH_INFO_NAME . '_sortcol'   => $orderBy,
                self::SEARCH_INFO_NAME . '_sortorder' => $order,
                self::SEARCH_INFO_NAME . '_page'      => $currentPage,
                self::SEARCH_INFO_NAME . '_pagesize'  => $pageSize,
            )
        );

        // Setup expectations

        $this->searchInfo
             ->expects($this->once())
             ->method('setSearch')
             ->with($this->equalTo($searchTerm));

        $this->searchInfo
             ->expects($this->once())
             ->method('setOrderBy')
             ->with($this->equalTo($orderBy));

        $this->searchInfo
             ->expects($this->once())
             ->method('setOrder')
             ->with($this->equalTo($order));

        $this->searchInfo
             ->expects($this->once())
             ->method('setCurrentPage')
             ->with($this->equalTo($currentPage));

        $this->searchInfo
             ->expects($this->once())
             ->method('setPageSize')
             ->with($this->equalTo($pageSize));

        // Test

        $result = $this->searchable->setValues($request);

        $this->assertEquals($this->searchable, $result, 'setValues did not return $this');
    }

    /**
     * Test that when getList is called and no repository is set an exception is thrown.
     *
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException        SclZfSearchable\Exception\RuntimeException
     * @expectedExceptionMessage A valid repository has not yet been set in SclZfSearchable\Searchable::getList()
     *
     * @return void
     */
    public function testGetListWithNoListRepository()
    {
        $this->searchable->getList();
    }

    /**
     * Test that when getList is called and no repository is set an exception is thrown.
     *
     * @covers SclZfSearchable\Searchable::setRepository
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException        SclZfSearchable\Exception\RuntimeException
     * @expectedExceptionMessage The list name has not yet been set in SclZfSearchable\Searchable::getList()
     *
     * @return void
     */
    public function testGetListWithNoListName()
    {
        $repository = $this->getMock('SclZfSearchable\SearchableRepositoryInterface');

        $this->searchable->setRepository($repository);

        $this->searchable->getList();
    }

    /**
     * Test that when getList and no search info object is set.
     *
     * @covers SclZfSearchable\Searchable::setRepository
     * @covers SclZfSearchable\Searchable::setListName
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException        SclZfSearchable\Exception\RuntimeException
     * @expectedExceptionMessage A repository method with the name 'doesNoExist' doesn't exist in SclZfSearchable\Searchable::getList()
     *
     * @return void
     */
    public function testGetListWithBadListName()
    {
        $repository = $this->getMock('SclZfSearchable\SearchableRepositoryInterface');

        $this->searchable->setRepository($repository);

        $this->searchable->setListName('doesNoExist');

        $this->searchable->getList();
    }

    /**
     * Test that when getList is called with a list name which doesn't exist an exception is thrown.
     *
     * @covers SclZfSearchable\Searchable::setRepository
     * @covers SclZfSearchable\Searchable::setListName
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException        SclZfSearchable\Exception\RuntimeException
     * @expectedExceptionMessage The search info has not been set yet in SclZfSearchable\Searchable::getList()
     *
     * @return void
     */
    public function testGetListWithNoSearchInfo()
    {
        $repository = $this->getMock(
            'SclZfSearchable\SearchableRepositoryInterface',
            array('theList', 'setSearchInfo')
        );

        $searchable = new Searchable();

        $searchable->setRepository($repository);

        $searchable->setListName('theList');

        $searchable->getList();
    }

    /**
     * Test that if the results from the repository are not an array or
     * Traversable an exception is thrown.
     *
     * @covers SclZfSearchable\Searchable::setRepository
     * @covers SclZfSearchable\Searchable::setListName
     * @covers SclZfSearchable\Searchable::getList
     * @expectedException        SclZfSearchable\Exception\DomainException
     * @expectedExceptionMessage The repository's 'theList' method did not return an array in SclZfSearchable\Searchable::getList()
     *
     * @return void
     */
    public function testGetListWithBadResults()
    {
        $repository = $this->getMock(
            'SclZfSearchable\SearchableRepositoryInterface',
            array('theList', 'setSearchInfo')
        );

        $repository->expects($this->once())
                   ->method('setSearchInfo')
                   ->with($this->equalTo($this->searchInfo));

        $repository->expects($this->once())
                   ->method('theList')
                   ->will($this->returnValue(1));

        $this->searchable->setRepository($repository);

        $this->searchable->setListName('theList');

        $this->searchable->getList();
    }

    /**
     * Test that if getList is called and all goes well the results are returned.
     *
     * @covers SclZfSearchable\Searchable::setRepository
     * @covers SclZfSearchable\Searchable::setListName
     * @covers SclZfSearchable\Searchable::getList
     *
     * @return void
     */
    public function testGetListWorksCorrectly()
    {
        $params = array('a', 'b');

        $results = array('item1', 'item2', 'item3');

        $repository = $this->getMock(
            'SclZfSearchable\SearchableRepositoryInterface',
            array('theList', 'setSearchInfo')
        );

        $repository->expects($this->once())
                   ->method('setSearchInfo')
                   ->with($this->equalTo($this->searchInfo));

        $repository->expects($this->once())
                   ->method('theList')
                   ->with($this->equalTo('a'), $this->equalTo('b'))
                   ->will($this->returnValue($results));

        $this->searchable->setRepository($repository);

        $this->searchable->setListName('theList', $params);

        $this->assertEquals($results, $this->searchable->getList());
    }
}

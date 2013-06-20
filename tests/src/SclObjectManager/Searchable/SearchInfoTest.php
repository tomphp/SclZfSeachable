<?php

namespace SclObjectManager\Searchable;

use PHPUnit_Framework_TestCase;
use Zend\Http\Request;

class SearchInfoTest extends PHPUnit_Framework_TestCase
{
    const CURRENT_PAGE = 5;
    const SEARCH       = 'search term';
    const ORDER_BY     = 'column7';
    const ORDER_ASC    = true;
    const PAGE_SIZE    = 25;

    const TEST_NAME    = 'random_name';

    protected $container;

    public function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');

        $this->container = new \stdClass();
        $this->container->currentPage = self::CURRENT_PAGE;
        $this->container->search      = self::SEARCH;
        $this->container->orderBy     = self::ORDER_BY;
        $this->container->orderAsc    = self::ORDER_ASC;
        $this->container->pageSize    = self::PAGE_SIZE;
    }

    protected function paramName($name)
    {
        return self::TEST_NAME . '_' . $name;
    }

    protected function createRequest($searchInfo, $search, $currentPage, $orderBy, $order, $pageSize)
    {
        $request = new Request();

        $request->getPost()->set($searchInfo->searchParamName(), $search);

        $request->getQuery()->set($searchInfo->currentPageParamName(), $currentPage)
                            ->set($searchInfo->orderByParamName(), $orderBy)
                            ->set($searchInfo->orderParamName(), $order)
                            ->set($searchInfo->pageSizeParamName(), $pageSize);

        return $request;
    }

    private function createSearchInfo($container = null, $name = self::TEST_NAME)
    {
        if (null === $container) {
            $container = $this->container;
        }

        $searchInfo = new SearchInfo();

        $searchInfo->setContainer($container)
            ->setName($name);

        return $searchInfo;
    }

    public function testParamNames()
    {
        $container = new \stdClass();
        $searchInfo = $this->createSearchInfo($container, self::TEST_NAME);

        $this->assertEquals($this->paramName($searchInfo::SEARCH_PARAM), $searchInfo->searchParamName());
        $this->assertEquals($this->paramName($searchInfo::CURRENT_PAGE_PARAM), $searchInfo->currentPageParamName());
        $this->assertEquals($this->paramName($searchInfo::ORDERBY_PARAM), $searchInfo->orderByParamName());
        $this->assertEquals($this->paramName($searchInfo::ORDER_PARAM), $searchInfo->orderParamName());
        $this->assertEquals($this->paramName($searchInfo::PAGE_SIZE_PARAM), $searchInfo->pageSizeParamName());
    }

    /**
     * @ expectedException Exception
    public function testBadOrderByThrowsException()
    {
       $searchInfo = new SearchInfo($this->container);

        $searchInfo->setOrderBy('BadString?@#');
    }
     */

    public function testEmptyContainerGetsInitialised()
    {
        $container = new \stdClass();
        $searchInfo = $this->createSearchInfo($container, self::TEST_NAME);

        $this->assertEquals(1, $container->currentPage);
        $this->assertEquals('', $container->search);
        $this->assertEquals('', $container->orderBy);
        $this->assertEquals(true, $container->orderAsc);
        $this->assertEquals(SearchInfo::DEFAULT_PAGE_SIZE, $container->pageSize);
    }

    public function testInitializedContainerStaysIntact()
    {
        $searchInfo = $this->createSearchInfo();

        $this->assertEquals(self::CURRENT_PAGE, $this->container->currentPage);
        $this->assertEquals(self::SEARCH, $this->container->search);
        $this->assertEquals(self::ORDER_BY, $this->container->orderBy);
        $this->assertEquals(self::ORDER_ASC, $this->container->orderAsc);
        $this->assertEquals(self::PAGE_SIZE, $this->container->pageSize);
    }

    /**
     * @depends testInitializedContainerStaysIntact
     * @depends testParamNames
     */
    public function testGetters()
    {
        $searchInfo = $this->createSearchInfo();

        $this->assertEquals(self::TEST_NAME, $searchInfo->getName());

        $this->assertEquals(self::CURRENT_PAGE, $searchInfo->getCurrentPage());
        $this->assertEquals(self::SEARCH, $searchInfo->getSearch());
        $this->assertEquals(self::ORDER_BY, $searchInfo->getOrderBy());
        $this->assertEquals(self::ORDER_ASC, $searchInfo->getOrderAsc());
        $this->assertEquals(self::PAGE_SIZE, $searchInfo->getPageSize());
    }

    /**
     * @depends testGetters
     */
    public function testReset()
    {
        $searchInfo = $this->createSearchInfo();
        $searchInfo->reset();

        $this->assertEquals(1, $searchInfo->getCurrentPage());
        $this->assertEquals('', $searchInfo->getSearch());
        $this->assertEquals('', $searchInfo->getOrderBy());
        $this->assertTrue($searchInfo->getOrderAsc());
        $this->assertEquals(SearchInfo::DEFAULT_PAGE_SIZE, $searchInfo->getPageSize());
    }

    /**
     * @depends testReset
     */
    public function testResetOnSetNull()
    {
        $searchInfo = $this->createSearchInfo();

        $request = $this->createRequest($searchInfo, null, null, null, null, null);

        $searchInfo->setValues($request);

        $this->assertEquals(1, $searchInfo->getCurrentPage());
        $this->assertEquals('', $searchInfo->getSearch());
        $this->assertEquals('', $searchInfo->getOrderBy());
        $this->assertTrue($searchInfo->getOrderAsc());
        $this->assertEquals(SearchInfo::DEFAULT_PAGE_SIZE, $searchInfo->getPageSize());
    }

    /**
     * @depends testGetters
     */
    public function testSetAllValues()
    {
        $currentPage = '412';
        $search      = "testSetAllValues";
        $orderBy     = "testOrder";
        $order       = 'desc';
        $pageSize    = 50;

        $searchInfo = $this->createSearchInfo();
        $request = $this->createRequest($searchInfo, $search, $currentPage, $orderBy, $order, $pageSize);
        $searchInfo->setValues($request);

        $this->assertEquals($currentPage, $searchInfo->getCurrentPage());
        $this->assertEquals($search, $searchInfo->getSearch());
        $this->assertEquals($orderBy, $searchInfo->getOrderBy());
        $this->assertFalse($searchInfo->getOrderAsc());
        $this->assertEquals($pageSize, $searchInfo->getPageSize());
    }

    /**
     * @depends testGetters
     */
    public function testUnspecfiedValuesAreNotChanged()
    {
        $currentPage = '412';
        $search      = "testSetAllValues";
        $orderBy     = "testOrder";
        $order       = 'desc';
        $pageSize    = 50;

        $searchInfo = $this->createSearchInfo();

        $request = $this->createRequest($searchInfo, $search, null, null, null, null);
        $searchInfo->setValues($request);

        $this->assertEquals($search, $searchInfo->getSearch(), 'First: Search string');
        $this->assertEquals(1, $searchInfo->getCurrentPage(), 'First: Current page');
        $this->assertEquals(self::ORDER_BY, $searchInfo->getOrderBy(), 'First: Order by');
        $this->assertEquals(self::ORDER_ASC, $searchInfo->getOrderAsc(), 'First: Order ascending');
        $this->assertEquals(self::PAGE_SIZE, $searchInfo->getPageSize(), 'First: Page size');

        $request = $this->createRequest($searchInfo, null, $currentPage, $orderBy, $order, $pageSize);
        $searchInfo->setValues($request);

        $this->assertEquals($search, $searchInfo->getSearch(), 'Second: Search string');
        $this->assertEquals($currentPage, $searchInfo->getCurrentPage(), 'Second: Current page');
        $this->assertEquals($orderBy, $searchInfo->getOrderBy(), 'Second: Order by');
        $this->assertFalse($searchInfo->getOrderAsc(), 'Second: Order Ascending');
        $this->assertEquals($pageSize, $searchInfo->getPageSize(), 'Second: Page size');
    }
}

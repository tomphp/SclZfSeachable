<?php

namespace SclObjectManager;

use PHPUnit_Framework_TestCase;

class SearchableTest extends PHPUnit_Framework_TestCase
{
    protected $searchable;
    protected $objectManager;
    protected $searchInfo;

    public function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');


        $this->objectManager = $this->getMockBuilder('SclObjectManager\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchInfo = $this->getMockBuilder('SclObjectManager\Searchable\SearchInfo')
            ->disableOriginalConstructor()
            ->getMock();


        $this->searchable = new Searchable();
        $this->searchable->setObjectManager($this->objectManager);
        $this->searchable->setSearchInfo($this->searchInfo);
    }

    protected function searchInfoMethodTest($method)
    {
        $testValue = "test" . rand();

        $this->searchInfo->expects($this->once())
            ->method($method)
            ->will($this->returnValue($testValue));

        $result = $this->searchable->$method();

        $this->assertEquals($testValue, $result);
    }

    public function testSearchParamName()
    {
        $this->searchInfoMethodTest('searchParamName');
    }

    public function testCurrentPageParamName()
    {
        $this->searchInfoMethodTest('currentPageParamName');
    }

    public function testOrderByParamName()
    {
        $this->searchInfoMethodTest('orderByParamName');
    }

    public function testOrderParamName()
    {
        $this->searchInfoMethodTest('orderParamName');
    }

    public function testPageSizeParamName()
    {
        $this->searchInfoMethodTest('pageSizeParamName');
    }


    public function testGetSearch()
    {
        $this->searchInfoMethodTest('getSearch');
    }

    public function testGetCurrentPage()
    {
        $this->searchInfoMethodTest('getCurrentPage');
    }

    public function testGetOrderBy()
    {
        $this->searchInfoMethodTest('getOrderBy');
    }

    public function testGetOrderAsc()
    {
        $this->searchInfoMethodTest('getOrderAsc');
    }

    public function testGetPageSize()
    {
        $this->searchInfoMethodTest('getPageSize');
    }


    public function testGetList()
    {
        $testValue = "test" . rand();

        $this->objectManager->expects($this->once())
            ->method('fetchList')
            ->with($this->searchInfo)
            ->will($this->returnValue($testValue));

        $result = $this->searchable->getList();

        $this->assertEquals($testValue, $result);
    }
}

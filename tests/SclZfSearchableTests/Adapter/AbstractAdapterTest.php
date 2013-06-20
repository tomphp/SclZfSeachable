<?php

namespace SclZfSearchableTests\Adapter;

use SclZfSearchable\Adapter\AbstractAdapter;

abstract class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;

    protected $entityName;

    public function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');
    }

    // getEntityName tests

    public function testGetEntityName()
    {
        $name = $this->adapter->getEntityName();
        $this->assertEquals($this->entityName, $name);
    }

    // create tests

    public function testCreate()
    {
        $object = $this->adapter->create();
        $this->assertInstanceOf($this->entityName, $object);
    }

    // addSearchTerms tests

    public function testAddSearchTermsSkipsOnNull()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->never())
            ->method('where');

        $this->adapter->addSearchTerms($queryBuilder, null);
    }

    public function testAddSearchTermsSkipsOnSearchTermNull()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->never())
            ->method('where');

        $searchInfo = $this->getMockBuilder('SclObjectManager\Searchable\SearchInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $searchInfo->expects($this->atLeastOnce())
            ->method('getSearch')
            ->will($this->returnValue(null));

        $this->adapter->addSearchTerms($queryBuilder, $searchInfo);
    }

    abstract public function testAddSearchTerms();

    // setOrder tests

    public function testSetOrderSkipsOnNull()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->never())
            ->method('orderBy');

        $this->adapter->setOrder($queryBuilder, null);
    }

    public function testSetOrderSkipsOnNullSearchableSearchInfo()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->never())
            ->method('orderBy');

        $searchInfo = $this->getMockBuilder('SclObjectManager\Searchable\SearchInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $searchInfo->expects($this->atLeastOnce())
            ->method('getOrderBy')
            ->will($this->returnValue(null));

        $this->adapter->setOrder($queryBuilder, $searchInfo);
    }

    public function testSetOrderAsc()
    {
        $orderBy = "my_column";

        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with("e.$orderBy", 'ASC');

        $searchInfo = $this->getMockBuilder('SclObjectManager\Searchable\SearchInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $searchInfo->expects($this->atLeastOnce())
            ->method('getOrderBy')
            ->will($this->returnValue($orderBy));

        $searchInfo->expects($this->once())
            ->method('getOrderAsc')
            ->will($this->returnValue(true));

        $this->adapter->setOrder($queryBuilder, $searchInfo);
    }

    public function testSetOrderDesc()
    {
        $orderBy = "my_column";

        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with("e.$orderBy", 'DESC');

        $searchInfo = $this->getMockBuilder('SclObjectManager\Searchable\SearchInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $searchInfo->expects($this->atLeastOnce())
            ->method('getOrderBy')
            ->will($this->returnValue($orderBy));

        $searchInfo->expects($this->once())
            ->method('getOrderAsc')
            ->will($this->returnValue(false));


        $this->adapter->setOrder($queryBuilder, $searchInfo);
    }
}

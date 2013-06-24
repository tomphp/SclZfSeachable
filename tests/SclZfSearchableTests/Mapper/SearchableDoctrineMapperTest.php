<?php

namespace SclZfSearchableTests\Mapper;

use SclZfSearchable\Mapper\SearchableDoctrineMapper;

/**
 * Unit tests from {@see SearchableDoctrineMapper}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class SearchableDoctrineMapperTest extends \PHPUnit_Framework_TestCase
{
    const ENTITY_NAME = 'TestEntity';

    protected $mapper;

    protected $queryBuilder;

    protected $searchInfo;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->mapper = new SearchableDoctrineMapper(
            $this->getMock('Doctrine\Common\Persistence\ObjectManager'),
            $this->getMockBuilder('SclZfUtilities\Doctrine\FlushLock')
                 ->disableOriginalConstructor()
                 ->getMock(),
            self::ENTITY_NAME
        );

        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->searchInfo = $this->getMock('SclZfSearchable\SearchInfo\SearchInfoInterface');
    }

    /**
     * Test that if queryAddSearch() is called with not SearchInfo set that an exception is thrown.
     *
     * @covers            SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddSearch
     * @expectedException SclZfSearchable\Exception\RuntimeException
     *
     * @return void
     */
    public function testQueryAddSearchWithNoSearchInfo()
    {
        $this->mapper->queryAddSearch($this->queryBuilder);
    }

    /**
     * Test that when the search string is null the query builder is just returned.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddSearch
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchInfo
     *
     * @return void
     */
    public function testQueryAddSearchWithNullSearchTerm()
    {
        $result = $this->mapper->setSearchInfo($this->searchInfo);

        $this->assertEquals($this->mapper, $result, 'setSearchInfo didn\'t return $this');

        $this->searchInfo
             ->expects($this->once())
             ->method('getSearch')
             ->will($this->returnValue(null));

        $this->queryBuilder->expects($this->never())->method('where');

        $result = $this->mapper->queryAddSearch($this->queryBuilder);

        $this->assertEquals($this->queryBuilder, $result);
    }

    /**
     * Test that a where expression is correctly constructed.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddSearch
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchInfo
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchFields
     *
     * @return void
     */
    public function testQueryAddSearch()
    {
        $search = 'search-string';
        $field = 'column1';

        $this->searchInfo
             ->expects($this->atLeastOnce())
             ->method('getSearch')
             ->will($this->returnValue($search));

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
                     ->disableOriginalConstructor()
                     ->getMock();

        $orx = $this->getMockBuilder('Doctrine\ORM\Query\Expr\Orx')
                    ->disableOriginalConstructor()
                    ->getMock();

        $like = $this->getMockBuilder('Doctrine\ORM\Query\Expr\Comparison')
                     ->disableOriginalConstructor()
                     ->getMock();

        // Set up the expectations

        $this->queryBuilder
             ->expects($this->atLeastOnce())
             ->method('expr')
             ->will($this->returnValue($expr));

        $expr->expects($this->once())
             ->method('orx')
             ->will($this->returnValue($orx));

        $expr->expects($this->once())
             ->method('like')
             ->with($this->equalTo('e.' . $field), $this->equalTo(':search'))
             ->will($this->returnValue($like));

        $orx->expects($this->once())
             ->method('add')
             ->with($this->equalTo($like));

        $this->queryBuilder
             ->expects($this->once())
             ->method('where')
             ->with($this->equalTo($orx));

        $this->queryBuilder
             ->expects($this->once())
             ->method('setParameter')
             ->with($this->equalTo('search'), $this->equalTo("%$search%"));

        // Perform the tests

        $result = $this->mapper->setSearchInfo($this->searchInfo);

        $this->assertEquals($this->mapper, $result, 'setSearchInfo didn\'t return $this');

        $result = $this->mapper->setSearchFields(array($field));

        $this->assertEquals($this->mapper, $result, 'setSearchFields did not return $this');

        $result = $this->mapper->queryAddSearch($this->queryBuilder);

        $this->assertEquals($this->queryBuilder, $result, 'queryAddSearch didn\'t return $this');
    }

    /**
     * Test that if queryAddOrderBy() is called with not SearchInfo set that an exception is thrown.
     *
     * @covers            SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddOrderBy
     * @expectedException SclZfSearchable\Exception\RuntimeException
     *
     * @return void
     */
    public function testQueryAddOrderByWithNoSearchInfo()
    {
        $this->mapper->queryAddOrderBy($this->queryBuilder);
    }

    /**
     * Test that when the order by value is null the query builder is just returned.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddOrderBy
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchInfo
     *
     * @return void
     */
    public function testQueryAddOrderByWithNullSearchTerm()
    {
        $result = $this->mapper->setSearchInfo($this->searchInfo);

        $this->assertEquals($this->mapper, $result, 'setSearchInfo didn\'t return $this');

        $this->searchInfo
             ->expects($this->once())
             ->method('getOrderBy')
             ->will($this->returnValue(null));

        $this->queryBuilder->expects($this->never())->method('orderby');

        $result = $this->mapper->queryAddOrderBy($this->queryBuilder);

        $this->assertEquals($this->queryBuilder, $result);
    }

}

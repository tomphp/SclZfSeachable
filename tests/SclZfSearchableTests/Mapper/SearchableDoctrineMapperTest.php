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

    protected $entityManager;

    protected $queryBuilder;

    protected $searchInfo;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->mapper = new SearchableDoctrineMapper(
            $this->entityManager,
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
     * testCreateQueryBuilder
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::createQueryBuilder
     *
     * @return void
     */
    public function testCreateQueryBuilder()
    {
        $this->entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->will($this->returnValue($this->queryBuilder));

        $this->queryBuilder
             ->expects($this->once())
             ->method('select')
             ->with($this->equalTo('e'))
             ->will($this->returnValue($this->queryBuilder));

        $this->queryBuilder
             ->expects($this->once())
             ->method('from')
             ->with($this->equalTo(self::ENTITY_NAME), $this->equalTo('e'))
             ->will($this->returnValue($this->queryBuilder));

        $qb = $this->mapper->createQueryBuilder();

        $this->assertSame($this->queryBuilder, $qb);
    }

    /**
     * Test that activeSearchTerm() just bails with a NULL if no search info is set.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::activeSearchTerm
     *
     * @return void
     */
    public function testActiveSearchTermWithNoSearchInfo()
    {
        $this->assertNull($this->mapper->activeSearchTerm());
    }

    /**
     * Test that activeSearchTerm() just bails with a NULL if no search fields are set.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::activeSearchTerm
     *
     * @return void
     */
    public function testActiveSearchTermWithNoSearchFields()
    {
        $this->mapper->setSearchInfo($this->searchInfo);

        $this->assertNull($this->mapper->activeSearchTerm());
    }

    /**
     * Test that activeSearchTerm() just bails with a NULL if no search term is specified.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::activeSearchTerm
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchFields
     *
     * @return void
     */
    public function testActiveSearchTermWithNoSearchTerm()
    {
        $this->searchInfo
             ->expects($this->once())
             ->method('getSearch')
             ->will($this->returnValue(null));

        $this->mapper->setSearchInfo($this->searchInfo);

        $this->mapper->setSearchFields(array('field'));

        $this->assertNull($this->mapper->activeSearchTerm());
    }

    /**
     * Test that activeSearchTerm() just bails with an empty if no search term is specified.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::activeSearchTerm
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchFields
     *
     * @return void
     */
    public function testActiveSearchTermWithEmptySearchTerm()
    {
        $this->searchInfo
             ->expects($this->once())
             ->method('getSearch')
             ->will($this->returnValue(''));

        $this->mapper->setSearchInfo($this->searchInfo);

        $this->mapper->setSearchFields(array('field'));

        $this->assertNull($this->mapper->activeSearchTerm());
    }

    /**
     * Test that activeSearchTerm() returns the search term.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::activeSearchTerm
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchFields
     *
     * @return void
     */
    public function testActiveSearchTermWithSearchTerm()
    {
        $this->searchInfo
             ->expects($this->atLeastOnce())
             ->method('getSearch')
             ->will($this->returnValue('find-me'));

        $this->mapper->setSearchInfo($this->searchInfo);

        $this->mapper->setSearchFields(array('field'));

        $this->assertEquals(
            'find-me',
            $this->mapper->activeSearchTerm()
        );
    }

    /**
     * Test the creation of a search expression.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::createSearchExpression
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchFields
     */
    public function testCreateSearchExpression()
    {
        $expr = $this->getMock('Doctrine\ORM\Query\Expr');
        $orx  = $this->getMock('Doctrine\ORM\Query\Expr\Orx');

        $searchFields = array('field1');

        $this->queryBuilder
             ->expects($this->any())
             ->method('expr')
             ->will($this->returnValue($expr));

        $expr->expects($this->once())
             ->method('orx')
             ->will($this->returnValue($orx));

        $expr->expects($this->once())
             ->method('like')
             ->with($this->equalTo('e.field1'), $this->equalTo(':search'))
             ->will($this->returnValue('like-1'));

        $this->mapper->setSearchFields($searchFields);

        $this->mapper->createSearchExpression($this->queryBuilder, 'search-term');
    }

    /**
     * Test that if queryAddOrderBy() is called with no SearchInfo set then the
     * query builder is returned.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddOrderBy
     *
     * @return void
     */
    public function testQueryAddOrderByWithNoSearchInfo()
    {
        $qb = $this->mapper->queryAddOrderBy($this->queryBuilder);

        $this->assertSame($this->queryBuilder, $qb);
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

        $this->queryBuilder->expects($this->never())->method('orderBy');

        $result = $this->mapper->queryAddOrderBy($this->queryBuilder);

        $this->assertSame($this->queryBuilder, $result);
    }

    /**
     * Test that when the order by value is null the query builder is just returned.
     *
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::queryAddOrderBy
     * @covers SclZfSearchable\Mapper\SearchableDoctrineMapper::setSearchInfo
     *
     * @return void
     */
    public function testQueryAddOrderByComplete()
    {
        $fieldName = 'testfield';

        $result = $this->mapper->setSearchInfo($this->searchInfo);

        $this->assertEquals($this->mapper, $result, 'setSearchInfo didn\'t return $this');

        $this->searchInfo
             ->expects($this->atLeastOnce())
             ->method('getOrderBy')
             ->will($this->returnValue($fieldName));

        $this->searchInfo
             ->expects($this->once())
             ->method('getOrder')
             ->will($this->returnValue(\SclZfSearchable\SearchInfo\SearchInfoInterface::SORT_DESC));

        $field = 'e.' . $fieldName;

        $this->queryBuilder
             ->expects($this->once())
             ->method('orderBy')
             ->with($this->equalTo($field), $this->equalTo('DESC'));

        $result = $this->mapper->queryAddOrderBy($this->queryBuilder);

        $this->assertSame($this->queryBuilder, $result);
    }
}

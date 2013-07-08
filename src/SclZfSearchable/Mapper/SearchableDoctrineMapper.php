<?php

namespace SclZfSearchable\Mapper;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DPAdapter;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use SclZfSearchable\Exception\RuntimeException;
use SclZfSearchable\SearchableRepositoryInterface;
use SclZfSearchable\SearchInfo\SearchInfoInterface;
use SclZfUtilities\Mapper\GenericDoctrineMapper;
use Zend\Paginator\Paginator as ZendPaginator;

/**
 * Simple doctrine mapper which provides a searchable fetchall.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class SearchableDoctrineMapper extends GenericDoctrineMapper implements
    SearchableRepositoryInterface
{
    /**
     * The name to use for the entity in the query.
     */
    const ENTITY = 'e';

    /**
     * The info to used in the search.
     *
     * @var SearchInfoInterface
     */
    protected $searchInfo;

    /**
     * A list of fields to be used in the search.
     *
     * @var string[]
     */
    protected $searchFields = array();

    /**
     * {@inheritDoc}
     *
     * @param  SearchInfoInterface $searchInfo
     * @return self
     */
    public function setSearchInfo(SearchInfoInterface $searchInfo)
    {
        $this->searchInfo = $searchInfo;

        return $this;
    }

    /**
     * Set the list of fields to use in a search.
     *
     * @param  string[] $fields
     * @return self
     */
    public function setSearchFields(array $fields)
    {
        $this->searchFields = $fields;

        return $this;
    }

    /**
     * Create a QueryBuilder and set the select & from values.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ENTITY)
           ->from($this->entityName, self::ENTITY);

        return $qb;
    }

    /**
     * Returns the active search term.
     *
     * @return string|null NULL if there is no term to be searched by.
     */
    public function activeSearchTerm()
    {
        if (null === $this->searchInfo) {
            return null;
        }

        if (empty($this->searchFields)) {
            return null;
        }

        if (null == $this->searchInfo->getSearch()) {
            return null;
        }

        return $this->searchInfo->getSearch();
    }

    /**
     * Create an {@see Expr} object with the search part of the where clause.
     *
     * @param  QueryBuilder $qb
     * @param  string       $searchTerm The string to search for.
     * @return Expr         The search part of the where clause.
     */
    public function createSearchExpression(QueryBuilder $qb, $searchTerm)
    {
        $searchExpr = $qb->expr()->orX();

        foreach ($this->searchFields as $field) {
            $searchExpr->add($qb->expr()->like(self::ENTITY . '.' . $field, ':search'));
        }

        $expr = $searchExpr;

        // @todo Maybe move this outside
        $qb->setParameter('search', '%' . $this->searchInfo->getSearch() . '%');

        return $expr;
    }

    /**
     * Constructs the complete where clause from the search express and the given expression.
     *
     * @param  QueryBuilder        $qb
     * @param  Expr|Composite      $expr
     * @return Expr|Composite|null The full where clause or NULL.
     */
    public function whereClause(QueryBuilder $qb, $expr = null)
    {
        $searchTerm = $this->activeSearchTerm();

        if (null === $expr && null === $searchTerm) {
            return null;
        }

        if (null !== $searchTerm) {
            $searchExpr = $this->createSearchExpression($qb, $searchTerm);
        }

        if (isset($searchExpr) && null === $expr) {
            return $searchExpr;
        }

        if (!isset($searchExpr) && null !== $expr) {
            return $expr;
        }

        $where = $qb->expr()->andX();

        $where->add($expr);
        $where->add($searchExpr);

        return $where;
    }

    /**
     * Sets up the query's order by clause.
     *
     * @param  QueryBuilder     $qb
     * @return QueryBuilder
     * @throws RuntimeException When called with out searchInfo being set.
     */
    public function queryAddOrderBy(QueryBuilder $qb)
    {
        if (null === $this->searchInfo) {
            return $qb;
        }

        if (null === $this->searchInfo->getOrderBy()) {
            return $qb;
        }

        $field = self::ENTITY . '.' . $this->searchInfo->getOrderBy();

        $order = (SearchInfoInterface::SORT_ASC === $this->searchInfo->getOrder()) ? 'ASC' : 'DESC';

        $qb->orderBy($field, $order);

        return $qb;
    }

    /**
     * Paginates the results from a doctrine query.
     *
     * @param  Query         $query
     * @return ZendPaginator
     */
    protected function paginateQuery(Query $query)
    {
        $doctrinePaginator = new DoctrinePaginator($query);

        $paginator = new ZendPaginator(new DPAdapter($doctrinePaginator));

        $paginator->setItemCountPerPage($this->searchInfo->getPageSize());
        $paginator->setCurrentPageNumber($this->searchInfo->getCurrentPage());

        return $paginator;
    }


    /**
     * Applies the where clause to the
     *
     * @param  QueryBuilder   $qb
     * @param  Expr|Composite $expr
     * @return mixed
     */
    public function finalizeQuery(QueryBuilder $qb, $expr = null)
    {
        $where = $this->whereClause($qb, $expr);

        if (null !== $where) {
            $qb->where($where);
        }

        $this->queryAddOrderBy($qb);

        // @todo Maybe convert results to arrays

        if (null !== $this->searchInfo) {
            return $this->paginateQuery($qb->getQuery());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns all entities that match the search criteria.
     *
     * @return ZendPaginator|array
     */
    public function findAll()
    {
        // Just for speed
        if (null === $this->searchInfo) {
            return parent::fetchAll();
        }

        $qb = $this->createQueryBuilder();

        return $this->finalizeQuery($qb);
    }
}

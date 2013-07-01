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
     * Applies the given search terms to the query builder's where clause.
     *
     * @param  QueryBuilder     $qb
     * @return QueryBuilder
     * @throws RuntimeException When called with out searchInfo being set.
     */
    public function queryAddSearch(QueryBuilder $qb)
    {
        if (null === $this->searchInfo) {
            throw new RuntimeException(
                __METHOD__ . ' was called without SearchInfo being set.'
            );
        }

        if (empty($this->searchFields)) {
            throw new RuntimeException(
                __METHOD__ . ' was called with no search fields set being set.'
            );
        }

        if (null === $this->searchInfo->getSearch()) {
            return $qb;
        }

        $expr = $qb->expr()->orX();

        foreach ($this->searchFields as $field) {
            $expr->add($qb->expr()->like(self::ENTITY . '.' . $field, ':search'));
        }

        $qb->where($expr);

        $qb->setParameter('search', '%' . $this->searchInfo->getSearch() . '%');

        return $qb;
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
            throw new RuntimeException(
                __METHOD__ . ' was called without SearchInfo being set.'
            );
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
     * @param  Query $query
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
     * Returns all entities that match the search criteria.
     *
     * @return ZendPaginator|array
     */
    public function findAll()
    {
        if (null === $this->searchInfo) {
            return parent::fetchAll();
        }

        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ENTITY)
           ->from($this->entityName, self::ENTITY);

        $this->queryAddSearch($qb);

        $this->queryAddOrderBy($qb);

        // @todo Maybe convert this to an array?
        return $this->paginateQuery($qb->getQuery());
    }
}

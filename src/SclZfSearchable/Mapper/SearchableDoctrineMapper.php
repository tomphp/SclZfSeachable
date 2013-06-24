<?php

namespace SclZfSearchable\Mapper;

use Doctrine\ORM\QueryBuilder;
use SclZfSearchable\SearchableRepositoryInterface;
use SclZfSearchable\SearchInfo\SearchInfoInterface;
use SclZfUtilities\Mapper\GenericDoctrineMapper;

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
     * @param  QueryBuilder $qb
     * @return QueryBuilder
     */
    public function queryAddSearch(QueryBuilder $qb)
    {
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
     * @param  QueryBuilder $qb
     * @return QueryBuilder
     */
    public function queryAddOrderBy(QueryBuilder $qb)
    {
        if (null === $this->searchInfo->getOrderBy()) {
            return $qb;
        }

        $field = self::ENTITY . '.' . $this->searchInfo->getOrderBy();

        $order = (SearchInfoInterface::SORT_ASC === $this->searchInfo->getOrder()) ? 'ASC' : 'DESC';

        $qb->orderBy($field, $order);

        return $qb;
    }



    /**
     * Returns all entities that match the search criteria.
     *
     * @return array
     */
    public function fetchAll()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ENTITY)
           ->from($this->entityName, self::ENTITY);

        $this->queryAddSearch($qb);

        $this->queryAddOrderBy($qb);

        // @todo Maybe convert this to an array?
        return $qb->getQuery->getResult();
    }
}

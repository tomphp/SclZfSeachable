<?php

namespace SclZfSearchable\Mapper;

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
     * Returns all entities that match the search criteria.
     *
     * @return array
     * @todo   Write this method
     */
    public function fetchAll()
    {
    }
}

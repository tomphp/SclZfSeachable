<?php

namespace SclZfSearchable;

use SclZfSearchable\SearchInfo\SearchInfoInterface;

/**
 * Interface for setting the search info to be used for retrieving result sets.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface SearchableRepositoryInterface
{
    /**
     * Set the SearchInfo to be applied to the query.
     *
     * @param  SearchInfoInterface $searchInfo
     * @return self
     */
    public function setSearchInfo(SearchInfoInterface $searchInfo);
}

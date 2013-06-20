<?php

namespace SclZfSearchable;

/**
 * Interface for setting the search info to be used for retrieving result sets.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface SearchableInterface
{
    /**
     * Set the SearchInfo to be applied to the query.
     *
     * @param  SearchInfo $searchInfo
     * @return self
     */
    public function setSearchInfo(SearchInfo $searchInfo);
}

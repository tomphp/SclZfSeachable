<?php
namespace SclObjectManager\Searchable;

use SclObjectManager\Searchable\SearchInfo;

/**
 * Interface for Doctrine EntityRepository class which can be searched.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface SearchableRepositoryInterface
{
    /**
     * Set the SearchInfo to be applied to the query.
     *
     * @param SearchInfo $searchInfo
     */
    public function setSearchInfo(SearchInfo $searchInfo);
}

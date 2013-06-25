<?php

namespace SclZfSearchable\SearchInfo;

/**
 * Interface for a class which contains a set of search terms.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface SearchInfoInterface
{
    /**
     * Return results sorted ascending.
     */
    const SORT_ASC  = 'asc';

    /**
     * Return results sorted decending.
     */
    const SORT_DESC = 'desc';

    /**
     * Set an identifying name for this search info set.
     *
     * @param  string $name
     * @return self
     */
    public function setName($name);

    /**
     * Get the identifying name for this search info set.
     * @return string
     */
    public function getName();

    /**
     * Get the current search term.
     *
     * @return string
     */
    public function getSearch();

    /**
     * Set the search term to be searched for.
     *
     * @param  string $search
     * @return self
     */
    public function setSearch($search);

    /**
     * Get the column to be used to sort by.
     *
     * @return string
     */
    public function getOrderBy();

    /**
     * Set the column to be used to sort by.
     *
     * @param  string $orderBy
     * @return self
     */
    public function setOrderBy($orderBy);

    /**
     * Get the sort order.
     *
     * @return string Either the SORT_ASC or SORT_DESC constant.
     */
    public function getOrder();

    /**
     * Set if the order is ascending or decending.
     *
     * @param  string $order Either the SORT_ASC or SORT_DESC constant.
     * @return self
     */
    public function setOrder($order);

    /**
     * Get the current display page number.
     *
     * @return int
     */
    public function getCurrentPage();

    /**
     * Set which page to display.
     *
     * @param  int $page
     * @return self
     */
    public function setCurrentPage($page);

    /**
     * Get the number of items being shown on a page.
     *
     * @return int
     */
    public function getPageSize();

    /**
     * Set the number of items to show on a page.
     *
     * @param  int  $pageSize
     * @return self
     */
    public function setPageSize($pageSize);

    /**
     * Clears all current search parameters.
     *
     * @return void
     */
    public function reset();
}

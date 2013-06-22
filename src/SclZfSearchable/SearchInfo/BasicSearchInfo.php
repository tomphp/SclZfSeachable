<?php

namespace SclZfSearchable\SearchInfo;

use SclZfSearchable\Exception\DomainException;

/**
 * A basic implementation of a SearchInfo class.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class BasicSearchInfo implements SearchInfoInterface
{
    /**
     * The default number of items to show per page.
     */
    const DEFAULT_PAGE_SIZE = 15;

    /**
     * @var string
     */
    protected $name;

    /**
     * The search string.
     *
     * @var string
     */
    protected $search = null;

    /**
     * The field name to sort by.
     *
     * @var string
     */
    protected $orderBy = null;

    /**
     * Is the sort ascending or decending.
     *
     * Can either by self::SORT_ASC or self::SORT_DESC.
     *
     * @var string
     */
    protected $order = self::SORT_ASC;

    /**
     * The number of the page to display.
     *
     * @var int
     */
    protected $currentPage = 1;

    /**
     * The number of results to dispaly per page.
     *
     * @var int
     */
    protected $pageSize = self::DEFAULT_PAGE_SIZE;

    /**
     * {@inheritDoc}
     *
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $search
     * @return self
     */
    public function setSearch($search)
    {
        $this->search = (string) $search;

        $this->setCurrentPage(1);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * {@inheritDoc}
     *
     * @param  string $orderBy
     * @throws DomainException If $order by contains illegal characters.
     * @return self
     */
    public function setOrderBy($orderBy)
    {
        if (preg_match('/[^A-Za-z0-9_.-]/', $orderBy)) {
            throw new DomainException('Order by string contains illegal characters.');
        }

        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritDoc}
     *
     * @param  string $order
     * @return self
     */
    public function setOrder($order)
    {
        if (!in_array($order, array(self::SORT_ASC, self::SORT_DESC))) {
            throw new DomainException(
                sprintf(
                    'Set order expects "%s" or "%s" got "%s" in %s',
                    self::SORT_ASC,
                    self::SORT_DESC,
                    is_object($order) ? get_class($order) : gettype($order),
                    __METHOD__
                )
            );
        }

        $this->order = $order;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * {@inheritDoc}
     *
     * @param  int  $page
     * @return self
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = (int) $page;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * {@inheritDoc}
     *
     * @param int   $pageSize
     * @return self
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = (int) $pageSize;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function reset()
    {
        $this->search  = null;
        $this->orderBy = null;

        $this->setOrder(self::SORT_ASC)
             ->setCurrentPage(1)
             ->setPageSize(self::DEFAULT_PAGE_SIZE);
    }
}

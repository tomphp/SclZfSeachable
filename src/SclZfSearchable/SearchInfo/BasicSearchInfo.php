<?php

namespace SclZfSearchable\Searchable;

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
     * @param string $name
     * @return SearchInfo
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->container->currentPage;
    }

    /**
     * @param integer $page
     * @return SearchInfo
     */
    public function setCurrentPage($page)
    {
        if (null !== $page) {
            $this->container->currentPage = (int)$page;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->container->search;
    }

    /**
     * @param string $search
     * @return SearchInfo
     */
    public function setSearch($search)
    {
        if (null !== $search) {
            $this->container->search = $search;
            $this->container->currentPage = 1;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->container->orderBy;
    }

    /**
     * @param string $orderBy
     * @throws \Exception
     * @return SearchInfo
     */
    public function setOrderBy($orderBy)
    {
        if (null !== $orderBy) {
            if (preg_match('/[^A-Za-z0-9_.-]/', $orderBy)) {
                throw new \Exception('Order by string contains illegal characters.');
            }
            $this->container->orderBy = $orderBy;
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function getOrderAsc()
    {
        return $this->container->orderAsc;
    }

    /**
     * @param mixed $order
     * @return SearchInfo
     */
    public function setOrderAsc($order)
    {
        if (null !== $order) {
            if (is_string($order)) {
                $order = ($order == 'asc');
            }

            $this->container->orderAsc = (boolean)$order;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getPageSize()
    {
        return $this->container->pageSize;
    }

    /**
     * @param integer $pageSize
     * @return SearchInfo
     */
    public function setPageSize($pageSize)
    {
        if (null !== $pageSize) {
            $this->container->pageSize = (int)$pageSize;
        }
        return $this;
    }



    /**
     *
     */
    public function reset()
    {
        $this->container->currentPage = 1;
        $this->container->search = null;
        $this->container->orderBy = null;
        $this->container->orderAsc = true;
        $this->container->pageSize = self::DEFAULT_PAGE_SIZE;
    }
}

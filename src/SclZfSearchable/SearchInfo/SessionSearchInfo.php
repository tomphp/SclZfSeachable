<?php

namespace SclZfSearchable\Searchable;

class SearchInfo
{
    /**
     * The default number of items to show per page.
     */
    const DEFAULT_PAGE_SIZE = 15;

    /**
     * @var string
     */
    private $name;

    /**
     * @var unknown_type
     */
    protected $container;

    const SEARCH_PARAM       = 'search';
    const CURRENT_PAGE_PARAM = 'page';
    const ORDERBY_PARAM      = 'sortcol';
    const ORDER_PARAM        = 'sortorder';
    const PAGE_SIZE_PARAM    = 'pagesize';

    /**
     * @param object $container
     * @return SearchInfo
     */
    public function setContainer($container)
    {
        $this->container = $container;

        if (!isset($this->container->currentPage)) {
            $this->reset();
        }

        return $this;
    }

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
     * @return string
     */
    public function searchParamName()
    {
        return $this->name . '_' . self::SEARCH_PARAM;
    }

    /**
     * @return string
     */
    public function currentPageParamName()
    {
        return $this->name . '_' . self::CURRENT_PAGE_PARAM;
    }

    /**
     * @return string
     */
    public function orderByParamName()
    {
        return $this->name . '_' . self::ORDERBY_PARAM;
    }

    /**
     * @return string
     */
    public function orderParamName()
    {
        return $this->name . '_' . self::ORDER_PARAM;
    }

    /**
     * @return string
     */
    public function pageSizeParamName()
    {
        return $this->name . '_' . self::PAGE_SIZE_PARAM;
    }


    /**
     * @param \Zend\Http\Request $request
     */
    public function setValues(\Zend\Http\Request $request)
    {
        $search      = $request->getPost()->get($this->searchParamName());
        $currentPage = $request->getQuery()->get($this->currentPageParamName());
        $orderBy     = $request->getQuery()->get($this->orderByParamName());
        $order       = $request->getQuery()->get($this->orderParamName());
        $pageSize    = $request->getQuery()->get($this->pageSizeParamName());

        if (null === $search
            && null === $currentPage
            && null === $orderBy
            && null === $order
        ) {
            $this->reset();
            return;
        }

        $this->setSearch($search);
        $this->setCurrentPage($currentPage);
        $this->setOrderBy($orderBy);
        $this->setOrderAsc($order);
        $this->setPageSize($pageSize);
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

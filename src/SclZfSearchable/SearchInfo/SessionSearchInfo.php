<?php

namespace SclZfSearchable\Searchable;

use SclZfSearchable\Exception\RuntimeError;
use Zend\Session\Container;

class SessionSearchInfo implements SearchInfoInterface
{
    /**
     * The default number of items to show per page.
     */
    const DEFAULT_PAGE_SIZE = 15;

    const KEY_SEARCH    = 'search';
    const KEY_ORDER_BY  = 'orderBy';
    const KEY_ORDER     = 'order';
    const KEY_PAGE_NUM  = 'currentPage';
    const KEY_PAGE_SIZE = 'pageSize';

    /**
     * The session container.
     *
     * @var Container
     */
    protected $container;

    /**
     * @param  Container $container
     * @return self
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        $name = $this->name;

        if (!isset($this->container->$name)) {
            $this->container->$name = array();

            $this->reset();
        }

        return $this;
    }

    /**
     * Stores a value to the session container.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     * @throws RuntimeError If the session container has not been set yet.
     */
    protected function storeValue($key, $value)
    {
        if (null === $this->container) {
            throw RuntimeError(
                'Session container has not been set yet'
                ' in ' . __METHOD__ . '()'
            );
        }

        $name = $this->name;

        $this->container->$name[$key] = $value;
    }

    /**
     * Stores a value to the session container.
     *
     * @param  string $key
     * @return void
     * @throws RuntimeError If the session container has not been set yet.
     */
    protected function retrieveValue($key)
    {
        if (null === $this->container) {
            throw RuntimeError(
                'Session container has not been set yet'
                ' in ' . __METHOD__ . '()'
            );
        }

        $name = $this->name;

        if (!isset($this->container->$name[$key])) {
            return null;
        }

        return $this->container->$name[$key];
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->retrieveValue(self::KEY_SEARCH);
    }

    /**
     * @param string $search
     * @return SearchInfo
     */
    public function setSearch($search)
    {
        $this->storeValue(self::KEY_SEARCH, (string) $search);

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->retrieveValue(self::KEY_ORDER_BY);
    }

    /**
     * @param string $orderBy
     * @throws \Exception
     * @return SearchInfo
     */
    public function setOrderBy($orderBy)
    {
        if (preg_match('/[^A-Za-z0-9_.-]/', $orderBy)) {
            throw new DomainException('Order by string contains illegal characters.');
        }

        $this->storeValue(self::KEY_ORDER_BY, (string) $orderBy);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getOrder()
    {
        return $this->retrieveValue(self::KEY_ORDER);
    }

    /**
     * @param mixed $order
     * @return SearchInfo
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

        $this->storeValue(self::KEY_ORDER, $order);

        return $this;
    }

    /**
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->retrieveValue(self::KEY_PAGE_NUM);
    }

    /**
     * @param integer $page
     * @return SearchInfo
     */
    public function setCurrentPage($page)
    {
        $this->storeValue(self::KEY_PAGE_NUM, (int) $page);

        return $this;
    }

    /**
     * @return integer
     */
    public function getPageSize()
    {
        return $this->retrieveValue(self::KEY_PAGE_SIZE);
    }

    /**
     * @param integer $pageSize
     * @return SearchInfo
     */
    public function setPageSize($pageSize)
    {
        $this->storeValue(self::KEY_PAGE_SIZE, (int) $pageSize);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function reset()
    {
        $this->storeValue(self::KEY_SEARCH, null);
        $this->storeValue(self::KEY_ORDER_BY, null);

        $this->setOrder(self::SORT_ASC)
             ->setCurrentPage(1)
             ->setPageSize(self::DEFAULT_PAGE_SIZE);
    }
}

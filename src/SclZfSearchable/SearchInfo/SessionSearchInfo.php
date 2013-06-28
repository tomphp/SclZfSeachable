<?php

namespace SclZfSearchable\SearchInfo;

use SclZfSearchable\Exception\RuntimeException;
use SclZfSearchable\Exception\DomainException;
use Zend\Session\Container;

/**
 * An SearchInfo implementation which stores it values in session.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
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
     * @todo   Maybe use the constructor to set this.
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
            throw new RuntimeException(
                'Session container has not been set yet'
                . ' in ' . __METHOD__ . '()'
            );
        }

        $name = $this->name;

        $this->container->$name[$key] = $value;
    }

    /**
     * Stores a value to the session container.
     *
     * @param  string       $key
     * @return void
     * @throws RuntimeError If the session container has not been set yet.
     */
    protected function retrieveValue($key)
    {
        if (null === $this->container) {
            throw RuntimeError(
                'Session container has not been set yet'
                . ' in ' . __METHOD__ . '()'
            );
        }

        $name = $this->name;

        if (!isset($this->container->$name[$key])) {
            return null;
        }

        return $this->container->$name[$key];
    }

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
        return $this->retrieveValue(self::KEY_SEARCH);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string $search
     * @return self
     */
    public function setSearch($search)
    {
        $this->storeValue(self::KEY_SEARCH, (string) $search);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->retrieveValue(self::KEY_ORDER_BY);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string          $orderBy
     * @throws DomainException When $orderBy contains bad characters.
     * @return self
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
     * {@inheritDoc}
     *
     * @return bool
     */
    public function getOrder()
    {
        return $this->retrieveValue(self::KEY_ORDER);
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

        $this->storeValue(self::KEY_ORDER, $order);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->retrieveValue(self::KEY_PAGE_NUM);
    }

    /**
     * {@inheritDoc}
     *
     * @param  int  $page
     * @return self
     */
    public function setCurrentPage($page)
    {
        $this->storeValue(self::KEY_PAGE_NUM, (int) $page);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->retrieveValue(self::KEY_PAGE_SIZE);
    }

    /**
     * {@inheritDoc}
     *
     * @param  int  $pageSize
     * @return self
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

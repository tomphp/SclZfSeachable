<?php

namespace SclObjectManager;

use SclObjectManager\Searchable\SearchInfo;
use SclObjectManager\ObjectManagerInterface;

/**
 * Class which makes a list returned from object manager searchable.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Searchable
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var SearchInfo
     */
    protected $searchInfo;

    /**
     * @var string
     */
    protected $listName;

    /**
     * @var array
     */
    protected $params;

    /**
     * Set the object manager to be used to retrieve the objects.
     *
     * @param ObjectManagerInterface $objectManager
     * @return self
     */
    public function setObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;

        return $this;
    }

    /**
     * Sets the list name to be searched.
     *
     * @param string $listName
     * @return self
     */
    public function setListName($listName, $params = null)
    {
        $this->listName = (string) $listName;
        $this->params = $params;

        return $this;
    }

    /**
     * @param SearchInfo $searchInfo
     * @return self
     */
    public function setSearchInfo(SearchInfo $searchInfo)
    {
        $this->searchInfo = $searchInfo;
        return $this;
    }

    /**
     * @return SearchInfo
     */
    public function getSearchInfo()
    {
        return $this->searchInfo;
    }

    /**
     * @return \Traversable
     */
    public function getList()
    {
        return $this->objectManager->fetchList($this->listName, $this->params, $this->searchInfo);
    }

    /*
     * Methods which route through to SearchInfo
     */

    /**
     * @return string
     */
    public function searchParamName()
    {
        return $this->searchInfo->searchParamName();
    }

    /**
     * @return string
     */
    public function currentPageParamName()
    {
        return $this->searchInfo->currentPageParamName();
    }

    /**
     * @return string
     */
    public function orderByParamName()
    {
        return $this->searchInfo->orderByParamName();
    }

    /**
     * @return string
     */
    public function orderParamName()
    {
        return $this->searchInfo->orderParamName();
    }

    /**
     * @return string
     */
    public function pageSizeParamName()
    {
        return $this->searchInfo->pageSizeParamName();
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->searchInfo->getSearch();
    }

    /**
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->searchInfo->getCurrentPage();
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->searchInfo->getOrderBy();
    }

    /**
     * @return boolean
     */
    public function getOrderAsc()
    {
        return $this->searchInfo->getOrderAsc();
    }

    /**
     * @return integer
     */
    public function getPageSize()
    {
        return $this->searchInfo->getPageSize();
    }
}

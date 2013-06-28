<?php

namespace SclZfSearchable;

use SclZfSearchable\Exception\DomainException;
use SclZfSearchable\Exception\RuntimeException;
use SclZfSearchable\SearchInfo\SearchInfoInterface;

/**
 * Class which makes a list returned from object manager searchable.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Searchable
{
    /**
     * The name of the search field input.
     */
    const SEARCH_PARAM       = 'search';

    /**
     * The name of the variable containing the current page number.
     */
    const CURRENT_PAGE_PARAM = 'page';

    /**
     * The name of the variable containing the name of the column to sort by.
     */
    const ORDERBY_PARAM      = 'sortcol';

    /**
     * The name of the variable containing the sort order.
     */
    const ORDER_PARAM        = 'sortorder';

    /**
     * The name of the variable which contains number of results to show per page.
     */
    const PAGE_SIZE_PARAM    = 'pagesize';

    /**
     * The object which retrieves objects from storage.
     *
     * @var SearchableRepositoryInterface
     */
    protected $repository;

    /**
     * @var SearchInfoInterface
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
     * @param  SearchableRepositoryInterface $repository
     * @return self
     */
    public function setRepository(SearchableRepositoryInterface $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Sets the list name to be searched.
     *
     * @param  string $listName
     * @return self
     */
    public function setListName($listName, array $params = array())
    {
        $this->listName = (string) $listName;

        $this->params = $params;

        return $this;
    }

    /**
     * @param  SearchInfoInterface $searchInfo
     * @return self
     */
    public function setSearchInfo(SearchInfoInterface $searchInfo)
    {
        $this->searchInfo = $searchInfo;

        return $this;
    }

    /**
     * @return SearchInfoInterface
     */
    public function getSearchInfo()
    {
        return $this->searchInfo;
    }

    /**
     * Get the searchable repository to return the results.
     *
     * @return array|\Traversable
     * @throws RuntimeException When no repository has been set.
     * @throws RuntimeException When the list name has not been set.
     * @throws RuntimeException When the list doesn't exits.
     * @throws RuntimeException If $this->searchInfo has not been set.
     * @throws DomainException If the repository method doesn't return an array.
     */
    public function getList()
    {
        if (!$this->repository instanceof SearchableRepositoryInterface) {
            throw new RuntimeException(
                'A valid repository has not yet been set'
                . ' in ' . __METHOD__ . '()'
            );
        }

        if (null === $this->listName) {
            throw new RuntimeException(
                'The list name has not yet been set'
                . ' in ' . __METHOD__ . '()'
            );
        }

        if (!method_exists($this->repository, $this->listName)) {
            throw new RuntimeException(
                "A repository method with the name '{$this->listName}' doesn't exist"
                . ' in ' . __METHOD__ . '()'
            );
        }

        if (null === $this->searchInfo) {
            throw new RuntimeException(
                'The search info has not been set yet'
                . ' in ' . __METHOD__ . '()'
            );
        }

        $this->repository->setSearchInfo($this->searchInfo);

        $results = call_user_func_array(
            array($this->repository, $this->listName),
            $this->params
        );

        if (!is_array($results) && !$results instanceof \Traversable) {
            throw new DomainException(
                "The repository's '{$this->listName}' method did not return an array"
                . ' in ' . __METHOD__ . '()'
            );
        }

        return $results;
    }

    /**
     * The name to be used for the variable containing the search field value.
     *
     * @return string
     */
    public function searchParamName()
    {
        return $this->searchInfo->getName() . '_' . self::SEARCH_PARAM;
    }

    /**
     * The name to be used for the variable containing the current page number.
     *
     * @return string
     */
    public function currentPageParamName()
    {
        return $this->searchInfo->getName() . '_' . self::CURRENT_PAGE_PARAM;
    }

    /**
     * The name to be used for the variable containing the column to sort by.
     *
     * @return string
     */
    public function orderByParamName()
    {
        return $this->searchInfo->getName() . '_' . self::ORDERBY_PARAM;
    }

    /**
     * The name to be used for the variable containing the sort order.
     *
     * @return string
     */
    public function orderParamName()
    {
        return $this->searchInfo->getName() . '_' . self::ORDER_PARAM;
    }

    /**
     * The name to be used for the variable containing the number of items on the page.
     *
     * @return string
     */
    public function pageSizeParamName()
    {
        return $this->searchInfo->getName() . '_' . self::PAGE_SIZE_PARAM;
    }

    /**
     * Sets a search info parameter to a value if the value is not null.
     *
     * @param SearchInfoInterface $searchInfo searchInfo
     * @param mixed $method method
     * @param mixed $value value
     * @return void
     */
    protected function setIfNotNull(SearchInfoInterface $searchInfo, $method, $value)
    {
        if (null === $value) {
            return;
        }

        $searchInfo->$method($value);
    }

    /**
     * Updates the search values from the current request.
     *
     * @param  \Zend\Http\Request $request
     * @return self
     * @throws RuntimeException When called before searchInfo is set.
     */
    public function setValues(\Zend\Http\Request $request)
    {
        $searchInfo = $this->getSearchInfo();

        if (null === $searchInfo) {
            throw new RuntimeException('setValues was called before $searchInfo was set.');
        }

        $search      = $request->getPost()->get($this->searchParamName());
        $orderBy     = $request->getQuery()->get($this->orderByParamName());
        $order       = $request->getQuery()->get($this->orderParamName());
        $currentPage = $request->getQuery()->get($this->currentPageParamName());
        $pageSize    = $request->getQuery()->get($this->pageSizeParamName());

        if (null === $search
            && null === $orderBy
            && null === $order
            && null === $currentPage
        ) {
            $searchInfo->reset();

            return $this;
        }

        $this->setIfNotNull($searchInfo, 'setSearch', $search);
        $this->setIfNotNull($searchInfo, 'setOrderBy', $orderBy);
        $this->setIfNotNull($searchInfo, 'setOrder', $order);
        $this->setIfNotNull($searchInfo, 'setCurrentPage', $currentPage);
        $this->setIfNotNull($searchInfo, 'setPageSize', $pageSize);

        return $this;
    }
}

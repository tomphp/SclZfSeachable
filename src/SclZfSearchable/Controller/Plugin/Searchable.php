<?php

namespace SclZfSearchable\Controller\Plugin;

use SclZfSearchable\Exception\RuntimeException;
use SclZfSearchable\Searchable as SearchableClass;
use SclZfSearchable\SearchableRepositoryInterface as SearchableRepository;
use SclZfSearchable\SearchInfo\SearchInfoInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin which returns a {@see \SclZfSearchable\Searchable}
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Searchable extends AbstractPlugin
{
    /**
     * The service locator string for creating an instance of {@see Searchable}.
     */
    const SEARCHABLE_SERVICE = 'SclZfSearchable\Searchable';

    /**
     * The service locator string for creating a SearchInfo instance.
     */
    const SEARCH_INFO_SERVICE = 'SclZfSearchable\SearchInfo\SearchInfoInterface';

    /**
     * Create a unique identification string from the controller, action and name.
     *
     * @param  string $name
     * @return string
     */
    /*
    protected function searchInfoName($name)
    {
        $controller = $this->getController();
        $action = $controller->params('action');

        return sprintf('%s\%sAction_%s', get_class($controller), $action, $name);
    }
    */

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected function getServiceLocator()
    {
        $contoller = $this->getController();

        return $contoller->getServiceLocator();
    }

    /**
     * @return \Zend\Http\Request
     */
    protected function getRequest()
    {
        $controller = $this->getController();

        return $controller->getRequest();
    }

    /**
     * @param  SearchableRepository $repository
     * @param  string               $name
     * @param  string               $listName
     * @param  array                $params
     * @return \Searchable
     * @throws RuntimeException     If the service locator doesn't return an instance if {@see SearchInfoInterface}.
     * @throws RuntimeExcepiton     If the service locator doesn't return an instance of {@see Searchable}.
     */
    public function __invoke(
        SearchableRepository $repository,
        $name,
        $listName,
        $params = null
    ) {
        $serviceLocator = $this->getServiceLocator();

        $searchInfo = $serviceLocator->get(self::SEARCH_INFO_SERVICE);

        if (!$searchInfo instanceof SearchInfoInterface) {
            throw new RuntimeException(
                '$searchInfo must be an instance of SclZfSearchable\SearchInfo\SearchInfoInterface; got '
                . (is_object($searchInfo) ? get_class($searchInfo) : gettype($searchInfo))
                . ' in ' . __METHOD__
            );
        }

        $searchable = $serviceLocator->get(self::SEARCHABLE_SERVICE);

        if (!$searchable instanceof SearchableClass) {
            throw new RuntimeException(
                '$searchable must be an instance of SclZfSearchable\Searchable; got '
                . (is_object($searchable) ? get_class($searchable) : gettype($searchable))
                . ' in ' . __METHOD__
            );
        }

        $searchInfo->setName($name);

        $searchable->setRepository($repository);

        $searchable->setSearchInfo($searchInfo);

        $searchable->setListName($listName, $params);

        $searchable->setValues($this->getRequest());

        return $searchable;
    }
}

<?php

namespace SclZfSearchable\Searchable;

use SclZfSearchable\ObjectManager;
use SclZfSearchable\Searchable;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

/**
 * Factory class for creating an instance of {@see Searchable}
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class SearchableBuilder implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param string        $containerName
     * @param ObjectManager $objectManager
     * @param string        $name
     * @param string        $listName
     * @param array         $params
     * @return Searchable
     */
    public function create($containerName, ObjectManager $objectManager, $name, $listName, $params = null)
    {
        $container = new Container($containerName);

        $searchInfo = $this->getServiceLocator()->get('SclZfSearchable\Searchable\SearchInfo');

        $searchInfo->setContainer($container)
            ->setName($name);

        $searchable = $this->getServiceLocator()->get('SclZfSearchable\Searchable');

        $searchable->setObjectManager($objectManager)
            ->setSearchInfo($searchInfo)
            ->setListName($listName, $params);

        return $searchable;
    }
}

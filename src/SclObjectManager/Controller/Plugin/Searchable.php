<?php
namespace SclObjectManager\Controller\Plugin;

use SclObjectManager\ObjectManager as TheObjectManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin which returns a {@see \SclObjectManager\Searchable}
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Searchable extends AbstractPlugin
{
    const SEARCH_BUILDER_SERVICE = 'SclObjectManager\Searchable\SearchableBuilder';

    /**
     * @param string $name
     * @return string
     */
    private function searchableContainerName($name)
    {
        $controller = $this->getController();
        $action = $controller->params('action');
        return sprintf('%s\%sAction_%s', get_class($controller), $action, $name);
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    private function getServiceLocator()
    {
        $contoller = $this->getController();
        return $contoller->getServiceLocator();
    }

    /**
     * @return \Zend\Http\Request
     */
    private function getRequest()
    {
        $controller = $this->getController();
        return $controller->getRequest();
    }

    /**
     * @param TheObjectMananger $objectManager
     * @param string            $name
     * @param string            $listName
     * @param array             $params
     * @return \SclObjectManager\Searchable
     */
    protected function __invoke(TheObjectManager $objectManager, $name, $listName, $params = null)
    {
        $containerName = $this->searchableContainerName($name);

        $builder = $this->getServiceLocator()->get(self::SEARCH_BUILDER_SERVICE);
        $searchable = $builder->create($containerName, $objectManager, $name, $listName, $params);

        $searchable->getSearchInfo()->setValues($this->getRequest());

        return $searchable;
    }
}

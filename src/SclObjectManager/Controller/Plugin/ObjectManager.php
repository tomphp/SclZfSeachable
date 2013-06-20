<?php

namespace SclObjectManager\Controller\Plugin;

use SclObjectManager\ObjectManager as TheObjectManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin for fetching an instance of the ObjectManager.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ObjectManager extends AbstractPlugin
{
    const OBJECT_MANAGER_SERVICE = 'SclObjectManager\ObjectManager';

    /**
     * Returns an instance of the object manager.
     *
     * @param string $adapter
     * @return TheObjectManager
     */
    public function __invoke($entityName = null)
    {
        $serviceLocator = $this->getController()->getServiceLocator();

        /* @var $objectManager TheObjectManager */
        $objectManager = $serviceLocator->get(self::OBJECT_MANAGER_SERVICE);

        if (null !== $entityName) {
            $objectManager->setEntityName($entityName);
        }

        return $objectManager;
    }
}

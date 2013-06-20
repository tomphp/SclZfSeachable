<?php
namespace SclObjectManager\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use SclObjectManager\EntityFormBuilder;

/**
 *
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class FormBuilder extends AbstractPlugin
{
    const FORM_BUILDER_SERVICE = 'SclObjectManager\EntityFormBuilder';

    /**
     * @var EntityFormBuilder
     */
    private $formBuilder;

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected function getServiceLocator()
    {
        $controller = $this->getController();
        return $controller->getServiceLocator();
    }

    /**
     * @param \SclObjectManager\ObjectManager $objectManager
     * @return EntityFormBuilder
     */
    public function __invoke($objectManager)
    {
        if (null == $this->formBuilder) {
            $serviceLocator = $this->getServiceLocator();
            $this->formBuilder = $serviceLocator->get(self::FORM_BUILDER_SERVICE);
            $this->formBuilder->setObjectManager($objectManager);
        }
        return $this->formBuilder;
    }
}

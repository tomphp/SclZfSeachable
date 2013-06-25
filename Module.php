<?php

namespace SclObjectManager;

use SclObjectManager\EntityFormBuilder;
use SclObjectManager\EntityFormBuilder\EntityManagerAssociation;

/**
 * @author Tom Oram
 */
class Module
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'controller_plugins' => array(
                'invokables' => array(
                    'getFormBuilder' => 'SclObjectManager\Controller\Plugin\FormBuilder',
                    'getSearchable'  => 'SclObjectManager\Controller\Plugin\Searchable',
                    'createObjectManager'  => 'SclObjectManager\Controller\Plugin\ObjectManager',
                ),
            ),
            'view_helpers' => array(
                'invokables' => array(
                    'sortableColumn' => 'SclObjectManager\View\Helper\SortableColumn',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'shared' => array(
                'SclObjectManager\ObjectManager'             => false,
                'SclObjectManager\Adapter\AutoObjectAdapter' => false,
                'SclObjectManager\Searchable'                => false,
                'SclObjectManager\Searchable\SearchInfo'     => false,
            ),
            'invokables' => array(
                'SclObjectManager\Adapter\AutoObjectAdapter'    => 'SclObjectManager\Adapter\AutoObjectAdapter',
                'SclObjectManager\Searchable'                   => 'SclObjectManager\Searchable',
                'SclObjectManager\Searchable\SearchableBuilder' => 'SclObjectManager\Searchable\SearchableBuilder',
                'SclObjectManager\Searchable\SearchInfo'        => 'SclObjectManager\Searchable\SearchInfo',
                'Zend\Form\Annotation\AnnotationBuilder'        => 'Zend\Form\Annotation\AnnotationBuilder',
            ),
            'factories' => array(
                'SclObjectManager\ObjectManager' => function ($sm) {
                    $entityManager = $sm->get('doctrine.entitymanager.orm_default');
                    $objectManager = new ObjectManager($entityManager);
                    return $objectManager;
                },
                'SclObjectManager\EntityFormBuilder' => function ($sm) {
                    $entityManager = $sm->get('doctrine.entitymanager.orm_default');
                    $request = $sm->get('Request');
                    $formBuilder = new EntityFormBuilder($entityManager, $request);
                    return $formBuilder;
                },
                'SclObjectManager\EntityFormBuilder\EntityManagerAssociation' => function ($sm) {
                    $entityManager = $sm->get('doctrine.entitymanager.orm_default');
                    return new EntityManagerAssociation($entityManager);
                }
            ),
        );
    }
}

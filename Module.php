<?php

namespace SclZfSearchable;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Zend Framework 2 module class for the SclZfSearchable module.
 *
 * @author Tom Oram
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     *
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
     * {@inheritDoc}
     *
     * @return array
     */
    public function getConfig()
    {
        return array(
            'controller_plugins' => array(
                'invokables' => array(
                    'getSearchable'  => 'SclZfSearchable\Controller\Plugin\Searchable',
                ),
            ),
            'view_helpers' => array(
                'invokables' => array(
                    'sortableColumn' => 'SclZfSearchable\View\Helper\SortableColumn',
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'shared' => array(
                'SclZfSearchable\Searchable'                     => false,
                'SclZfSearchable\SearchInfo\BasicSearchInfo'     => false,
                'SclZfSearchable\SearchInfo\SessionSearchInfo'   => false,
            ),
            'aliases' => array(
                'SclZfSearchable\SearchInfo\SearchInfoInterface' => 'SclZfSearchable\SearchInfo\SessionSearchInfo',
            ),
            'invokables' => array(
                'SclZfSearchable\Searchable'                   => 'SclZfSearchable\Searchable',
                'SclZfSearchable\SearchInfo\BasicSearchInfo'   => 'SclZfSearchable\SearchInfo\BasicSearchInfo',
                'scl_zf_searchable_session_container'          => 'Zend\Session\Container',
            ),
            'factories' => array(
                'SclZfSearchable\SearchInfo\SessionSearchInfo' => function ($sm) {
                    $searchInfo = new \SclZfSearchable\SearchInfo\SessionSearchInfo();

                    $searchInfo->setContainer($sm->get('scl_zf_searchable_session_container'));

                    return $searchInfo;
                }
            ),
        );
    }
}

<?php

namespace SclZfSearchableTests\Controller\Plugin;

use SclZfSearchable\Controller\Plugin\Searchable;

/**
 * Unit tests for {@see Searchable}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class SearchableTest extends \PHPUnit_Framework_TestCase
{
    protected $testName   = 'test-name';
    protected $testList   = 'theList';
    protected $testParams = array('test-params');

    /**
     * The instance being tested.
     *
     * @var Searchable
     */
    protected $plugin;

    protected $serviceLocator;

    protected $controller;

    protected $request;

    protected $repository;

    protected $searchInfo;

    protected $searchable;

    /**
     * Prepare the instances to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        // Set up ZF controller plugin mocks

        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->request = $this->getMock('Zend\Http\Request');

        $this->controller = $this->getMock('Zend\Mvc\Controller\AbstractController');

        $this->controller
             ->expects($this->any())
             ->method('getServiceLocator')
             ->will($this->returnValue($this->serviceLocator));

        $this->controller
             ->expects($this->any())
             ->method('getRequest')
             ->will($this->returnValue($this->request));

        // Set up the plugin to be tested

        $this->plugin = new Searchable();

        $this->plugin->setController($this->controller);

        // Set up test mocks

        $this->searchInfo = $this->getMock('SclZfSearchable\SearchInfo\SearchInfoInterface');

        $this->searchable = $this->getMock('SclZfSearchable\Searchable');

        $this->repository = $this->getMock('SclZfSearchable\SearchableRepositoryInterface');
    }

    /**
     * Invokes the plugin
     *
     * @return \SclZfSearchable\Searchable
     */
    protected function invoke()
    {
        return $this->plugin->__invoke(
            $this->repository,
            $this->testName,
            $this->testList,
            $this->testParams
        );
    }

    /**
     * Test that when the plugin is run and a bad SearchInfo object is returned an exception is thrown.
     *
     * @covers            SclZfSearchable\Controller\Plugin\Searchable::__invoke
     * @covers            SclZfSearchable\Controller\Plugin\Searchable::getServiceLocator
     * @expectedException SclZfSearchable\Exception\RuntimeException
     *
     * @return void
     */
    public function testInvokeWithBadSearchInfo()
    {
        $this->serviceLocator
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('SclZfSearchable\SearchInfo\SearchInfoInterface'))
             ->will($this->returnValue(7));

        $this->invoke();
    }

    /**
     * Test that when the plugin is run and a bad Seachable object is returned an exception is thrown.
     *
     * @covers            SclZfSearchable\Controller\Plugin\Searchable::__invoke
     * @covers            SclZfSearchable\Controller\Plugin\Searchable::getServiceLocator
     * @expectedException SclZfSearchable\Exception\RuntimeException
     *
     * @return void
     */
    public function testInvokeWithBadSearchable()
    {
        $this->serviceLocator
             ->expects($this->at(0))
             ->method('get')
             ->with($this->equalTo('SclZfSearchable\SearchInfo\SearchInfoInterface'))
             ->will($this->returnValue($this->searchInfo));

        $this->serviceLocator
             ->expects($this->at(1))
             ->method('get')
             ->with($this->equalTo('SclZfSearchable\Searchable'))
             ->will($this->returnValue('x'));

        $this->invoke();
    }

    /**
     * Test when the controller is invoked properly the correct objects are set up.
     *
     * @return void
     */
    public function testInvoke()
    {
        $this->serviceLocator
             ->expects($this->at(0))
             ->method('get')
             ->with($this->equalTo('SclZfSearchable\SearchInfo\SearchInfoInterface'))
             ->will($this->returnValue($this->searchInfo));

        $this->serviceLocator
             ->expects($this->at(1))
             ->method('get')
             ->with($this->equalTo('SclZfSearchable\Searchable'))
             ->will($this->returnValue($this->searchable));

        $this->searchInfo
             ->expects($this->once())
             ->method('setName')
             ->with($this->equalTo($this->testName));

        $this->searchable
             ->expects($this->once())
             ->method('setRepository')
             ->with($this->equalTo($this->repository));

        $this->searchable
             ->expects($this->once())
             ->method('setSearchInfo')
             ->with($this->equalTo($this->searchInfo));

        $this->searchable
             ->expects($this->once())
             ->method('setListName')
             ->with($this->equalTo($this->testList));

        $this->searchable
             ->expects($this->once())
             ->method('setValues')
             ->with($this->equalTo($this->request));

        $result = $this->invoke();

        $this->assertEquals($this->searchable, $result);
    }
}

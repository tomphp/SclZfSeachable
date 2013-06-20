<?php

namespace SclObjectManager;

use Zend\Http\Request;

class EntityFormBuilderTest extends AbstractReflectionTestCase
{
    protected $formBuilder;
    protected $entityManager;
    protected $request;
    protected $objectManager;
    protected $serviceManager;

    protected $form;

    public function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');

        $this->request = $this->getMockBuilder('Zend\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager = $this->getMockBuilder('SclObjectManager\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formBuilder = new EntityFormBuilder($this->entityManager, $this->request);
        $this->formBuilder->setObjectManager($this->objectManager)
            ->setServiceLocator($this->serviceManager);

        $this->form = $this->getMockBuilder('Zend\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSaveReturnsFalseIfNotPost()
    {
        $this->request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(false));

        $this->objectManager->expects($this->never())
            ->method('save');

        $result = $this->formBuilder->saveObject(new \stdClass(), $this->form);

        $this->assertFalse($result);
    }

    public function testSaveReturnsFalseIfNotValid()
    {
        $this->request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->objectManager->expects($this->never())
            ->method('save');

        $result = $this->formBuilder->saveObject(new \stdClass(), $this->form);

        $this->assertFalse($result);
    }

    public function testSaveSuccessful()
    {
        $this->request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $object = new \stdClass();

        $this->objectManager->expects($this->once())
            ->method('save')
            ->with($object);

        $result = $this->formBuilder->saveObject($object, $this->form);

        $this->assertTrue($result);
    }

    public function testGetForm()
    {
        $object = 'Object';
        $submit = 'Add';
        $returnValue = 'TheFinalForm';

        $result = $this->mockedThisMethod(
            'SclObjectManager\EntityFormBuilder',
            'getForm',
            array($object, $submit),
            function ($mockThis, $testCase) use ($object, $submit, $returnValue) {
                $form = 'Form';

                $builder = $testCase->getMockBuilder('Zend\Form\Annotation\AnnotationBuilder')
                    ->disableOriginalConstructor()
                    ->getMock();

                $builder->expects($testCase->once())
                    ->method('createForm')
                    ->with($object)
                    ->will($testCase->returnValue($form));

                $serviceManager = $testCase->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
                    ->disableOriginalConstructor()
                    ->getMock();

                $serviceManager->expects($testCase->once())
                    ->method('get')
                    ->with('Zend\Form\Annotation\AnnotationBuilder')
                    ->will($testCase->returnValue($builder));

                $mockThis->expects($testCase->once())
                    ->method('getServiceLocator')
                    ->will($testCase->returnValue($serviceManager));

                $mockThis->expects($testCase->once())
                    ->method('prepareForm')
                    ->with($form, $object, $submit)
                    ->will($testCase->returnValue($returnValue));
            }
        );

        $this->assertEquals($returnValue, $result);
    }

    public function testPrepareForm()
    {
        $object = new \stdClass();

        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');

        $form = $this->getMockBuilder('Zend\Form\Form')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
        $form->expects($this->once())
            ->method('setHydrator')
            ->with($hydrator);
        $form->expects($this->once())
            ->method('bind')
            ->with($object);

        $association = $this->getMockBuilder('SclObjectManager\EntityFormBuilder\EntityManagerAssociation')
            ->disableOriginalConstructor()
            ->getMock();
        $association->expects($this->once())
            ->method('associate')
            ->with($form, get_class($object));

        $this->serviceManager->expects($this->at(0))
            ->method('get')
            ->with('SclObjectManager\EntityFormBuilder\EntityManagerAssociation')
            ->will($this->returnValue($association));

        $this->serviceManager->expects($this->at(1))
            ->method('get')
            ->with('SclZfUtilities\Hydrator\DoctrineObjectHydrator')
            ->will($this->returnValue($hydrator));

        $this->formBuilder->prepareForm($form, $object, 'submit');
    }
}

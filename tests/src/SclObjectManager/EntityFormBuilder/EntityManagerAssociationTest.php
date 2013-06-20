<?php

namespace SclObjectManager\EntityFormBuilder;

class EntityManagerAssociationTest extends \SclObjectManager\AbstractReflectionTestCase
{
    protected $entityManager;
    protected $ema;

    public function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');

        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->ema = new EntityManagerAssociation($this->entityManager);
    }


    public function testSetAssociationSingleElement()
    {
        $element = $this->getMockBuilder('Zend\Form\Element')
            ->disableOriginalConstructor()
            ->getMock();

        $element->expects($this->once())
            ->method('setOptions')
            ->with($this->identicalTo(array('object_manager' => $this->entityManager)));

        $this->ema->setAssociation($element, 'asda');
    }

    public function testSetAssociationWithFieldset()
    {
        $className = 'asda';

        $element = $this->getMockBuilder('Zend\Form\Fieldset')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $this->mockedThisMethod(
            'SclObjectManager\EntityFormBuilder\EntityManagerAssociation',
            'setAssociation',
            array($element, $className),
            function ($ema, $testCase) use ($element, $className) {
                $ema->expects($testCase->once())
                    ->method('associate')
                    ->with($element, $className);
            }
        );
    }

    public function testGetAssociations()
    {
        $className = 'FancyClass';
        $return = 'Hi Everybody!';

        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $metadata->expects($this->once())
            ->method('getAssociationMappings')
            ->will($this->returnValue($return));

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($className)
            ->will($this->returnValue($metadata));

        $result = $this->ema->getAssociations($className);

        $this->assertEquals($return, $result);
    }

    public function testAssociateWithNoAssociations()
    {
        $className = 'TheClass';

        $this->mockedThisMethod(
            'SclObjectManager\EntityFormBuilder\EntityManagerAssociation',
            'associate',
            array(null, $className),
            function ($ema, $testCase) use ($className) {
                $ema->expects($testCase->once())
                    ->method('getAssociations')
                    ->with($className)
                    ->will($testCase->returnValue(array()));
            }
        );
    }

    public function testAssociateWithAssociation()
    {
        $className = 'TheClass';

        $fieldset = $this->getMockBuilder('Zend\Form\Fieldset')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockedThisMethod(
            'SclObjectManager\EntityFormBuilder\EntityManagerAssociation',
            'associate',
            array($fieldset, $className),
            function ($ema, $testCase) use ($className) {
                $association = array(
                    'assoc1' => array('targetEntity' => 'fancyEntity')
                );

                $ema->expects($testCase->once())
                    ->method('getAssociations')
                    ->with($className)
                    ->will($testCase->returnValue($association));

                $ema->expects($testCase->once())
                    ->method('setAssociation');
            }
        );
    }
}

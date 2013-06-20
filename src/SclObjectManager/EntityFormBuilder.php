<?php
/**
 * Contains the EntityFormBuilder class.
 *
 * @author Tom Oram
 */
namespace SclObjectManager;

use Doctrine\ORM\EntityManager;
use SclObjectManager\ObjectManager;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * This class constructs a form object from a given annotated object
 * and also handles the reading back of the form data and saving of
 * the object.
 *
 * @author Tom Oram
 */
class EntityFormBuilder implements ServiceLocatorAwareInterface
{
    /**
     * The Doctrine2 ORM Entity manager
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * An object manager configured to manage the object the form is for
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * The current Request object
     * @var Request
     */
    protected $request;

    /**
     * The Zend ServiceManager
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * Initialises the form builder with the EntityManager and Request objects.
     *
     * @param EntityManager $entityManager
     * @param Request $request
     */
    public function __construct(EntityManager $entityManager, Request $request)
    {
        $this->entityManager = $entityManager;
        $this->request = $request;
    }


    /**
     * Implementing from ServiceLocatorAwareInterface.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    /**
     * Returns the Service Manager.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->services;
    }

    /**
     * Sets the object manager which will be used to fetch & save the object.
     *
     * @param ObjectManager
     * @return EntityFormBuilder
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * This connects up the hydrators for sub-forms to the EntityManager
     * and binds to the object. It also adds a submit button to the form.
     *
     * @param \Zend\Form\Form $form
     * @param object $object
     * @param string $submit
     * @return \Zend\Form\Form
     */
    public function prepareForm($form, $object, $submit)
    {
        $entityManagerAssociation = $this->getServiceLocator()
            ->get('SclObjectManager\EntityFormBuilder\EntityManagerAssociation');

        $entityManagerAssociation->associate($form, get_class($object));

        $hydrator = $this->getServiceLocator()->get('SclZfUtilities\Hydrator\DoctrineObjectHydrator');
        $form->setHydrator($hydrator);

        $form->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => $submit,
                    'id' => 'submitbutton',
                ),
            )
        );

        $form->bind($object);

        return $form;
    }

    /**
     * Returns a form build from the provided object using Zend form annotations.
     *
     * @param object $object
     * @param string $submit
     * @return \Zend\Form\Form
     */
    public function getForm($object, $submit)
    {
        $builder = $this->getServiceLocator()->get('Zend\Form\Annotation\AnnotationBuilder');

        $form = $builder->createForm($object);

        return $this->prepareForm($form, $object, $submit);
    }

    /**
     * This method checks if the form is submitted and if it has and it valid
     * the object is saved. This method relies on object & form being pre-bound.
     *
     * @param object $object
     * @param \Zend\Form\Form $form
     * @param function $preSaveCallback
     * @return boolean True if the object has been saved
     */
    public function saveObject($object, $form, $preSaveCallback = null)
    {
        if (!$this->request->isPost()) {
            return false;
        }

        $form->setData($this->request->getPost());

        if (!$form->isValid()) {
            return false;
        }

        if (null !== $preSaveCallback) {
            $preSaveCallback($object);
        }

        $this->objectManager->save($object);

        return true;
    }
}

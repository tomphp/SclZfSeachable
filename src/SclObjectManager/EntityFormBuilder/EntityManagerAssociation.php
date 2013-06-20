<?php

namespace SclObjectManager\EntityFormBuilder;

use Zend\Form\Fieldset;

class EntityManagerAssociation
{
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAssociations($className)
    {
        return $this->entityManager->getClassMetadata($className)->getAssociationMappings();
    }

    public function associate($fieldSet, $className)
    {
        $associations = $this->getAssociations($className);

        foreach ($associations as $name => $info) {
            $this->setAssociation($fieldSet->get($name), $info['targetEntity']);
        }
    }

    /**
     * @param fieldSet
     */
    public function setAssociation($element, $className)
    {
        $element->setOptions(array("object_manager" => $this->entityManager));

        if ($element instanceof Fieldset) {
            $this->associate($element, $className);
        }
    }
}

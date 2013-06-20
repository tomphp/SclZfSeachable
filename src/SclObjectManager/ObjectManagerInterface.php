<?php

namespace SclObjectManager;

use SclObjectManager\Searchable\SearchInfo;

/**
 * Interface for an ObjectManager class.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface ObjectManagerInterface
{
    /**
     * Set the entity name to deal with.
     *
     * @param string $entityName
     */
    public function setEntityName($entityName);

    /**
     * Returns the paginator for the given list.
     *
     * @param string     $listName
     * @param array      $params
     * @param SearchInfo $searchInfo
     * @return \Zend\Paginator\Paginator
     */
    public function fetchList($listName, array $params = null, SearchInfo $searchInfo = null);

    /**
     * Fetch an object by it's ID.
     *
     * @param mixed $id
     * @return object
     */
    public function fetch($id);

    /**
     * Save the given object.
     *
     * @param object $object
     */
    public function save($object);

    /**
     * Delete the given object.
     *
     * @param unknown $object
     */
    public function delete($object);
}

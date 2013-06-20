<?php

namespace SclObjectManager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DPAdapter;
use SclObjectManager\Searchable\SearchableRepositoryInterface;
use SclObjectManager\Searchable\SearchInfo;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator as ZendPaginator;

/**
 * Object manager persistence.
 *
 * @author Tom Oram <tom@scl.co.uk>
 * @todo Expand to work with Doctrine or ZendDb
 */
class ObjectManager implements ObjectManagerInterface
{
    /**
     * Doctrine entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The name of the entity this manager is manager.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Initialise the object.
     *
     * @param EntityManager $entityManager
     * @param string        $entityName
     */
    public function __construct(EntityManager $entityManager, $entityName = '')
    {
        $this->entityManager = $entityManager;
        $this->entityName = (string) $entityName;
    }

    /**
     * Set the entity name to deal with.
     *
     * @param string $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = (string) $entityName;
    }

    /**
     * Returns a Zend Paginator.
     *
     * @param array|DoctrinePagainator $result
     * @return Paginator
     */
    protected function getPaginator($result)
    {
        if ($result instanceof DoctrinePaginator) {
            return new ZendPaginator(new DPAdapter($result));
        }

        return new ZendPaginator(new ArrayAdapter($result));
    }

    /**
     * Checks the $searchInfo parameter and applies it to the repository.
     *
     * @param SearchInfo|null $searchInfo
     * @return void
     */
    protected function applySearchInfo(EntityRepository $repository, $searchInfo)
    {
        if (null === $searchInfo) {
            return;
        }

        if (!$searchInfo instanceof SearchInfo) {
            throw new \InvalidArgumentException(
                sprintf(
                    '\SclObjectManager\Searchable\SearchInfo expected; got "%s" in %s (%d).',
                    is_object($searchInfo) ? get_class($searchInfo) : gettype($searchInfo),
                    __METHOD__,
                    __LINE__
                )
            );
        }

        if (!$repository instanceof SearchableRepositoryInterface) {
            throw new \Exception(
                'For SearchInfo to be use '
                . get_class($repository)
                . ' must implement \SclObjectManager\Searchable\SearchableRepositoryInterface'
            );
        }

        $repository->setSearchInfo($searchInfo);
    }

    /**
     * Gets the list from the repository.
     *
     * @param EntityRepository $repository
     * @param string           $listName
     * @param array            $params
     * @throws \InvalidArgumentException
     */
    protected function queryRepository(EntityRepository $repository, $listName, $params)
    {
        if (!method_exists($repository, $listName)) {
            throw new \InvalidArgumentException(
                    "Bad list name '{$listName}' given for repository for {$this->entityName}."
            );
        }

        if (is_array($params)) {
            return call_user_func_array(array($repository, $listName), $params);
        }

        return $repository->$listName();
    }

    /**
     * Returns the paginator for the given list.
     *
     * @param string     $listName
     * @param array      $params     Parameters to be passed to the list function
     * @param SearchInfo $searchInfo
     * @return \Zend\Paginator\Paginator
     */
    public function fetchList($listName, array $params = null, SearchInfo $searchInfo = null)
    {
        $repository = $this->entityManager->getRepository($this->entityName);

        $this->applySearchInfo($repository, $searchInfo);

        $result = $this->queryRepository($repository, $listName, $params);

        $paginator = $this->getPaginator($result);

        $pageSize = (null !== $searchInfo) ? $searchInfo->getPageSize() : SearchInfo::DEFAULT_PAGE_SIZE;
        $paginator->setItemCountPerPage($pageSize);

        if (null !== $searchInfo) {
            $paginator->setCurrentPageNumber($searchInfo->getCurrentPage());
        }

        return $paginator;
    }

    /**
     * Fetch an object by it's ID.
     *
     * @param mixed $id
     * @return object
     */
    public function fetch($id)
    {
        if (!(int)$id) {
            return null;
        }

        return $this->entityManager->find($this->entityName, $id);
    }

    /**
     * Create a new instance of the entity.
     *
     * @return object
     */
    public function create()
    {
        $className = $this->entityName;

        return new $className();
    }

    /**
     * Save the given object.
     *
     * @param object $object
     */
    public function save($object)
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    /**
     * Delete the given object.
     *
     * @param object $object
     */
    public function delete($object)
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }
}

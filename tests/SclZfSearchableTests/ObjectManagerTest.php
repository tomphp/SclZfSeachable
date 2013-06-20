<?php

namespace SclObjectManager;

use SclBusiness\Entity\Accounting\Customer;
use PHPUnit_Framework_TestCase;

class ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $objectAdapter;
    protected $objectManager;

    public function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');

        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectAdapter = $this->getMock('SclObjectManager\Adapter\ObjectAdapterInterface');

        $this->objectManager = new ObjectManager($this->entityManager);
        $this->objectManager->setAdapter($this->objectAdapter);
    }

    public function testCreateReturnsCorrectType()
    {
        $object = new \stdClass();

        $this->objectAdapter->expects($this->once())
            ->method('create')
            ->will($this->returnValue($object));

        $result = $this->objectManager->create();
        $this->assertEquals($object, $result);
    }

    public function testFetchWithBadIdsReturnsNull()
    {
        // Make sure it doesn't try to load anything with these ids
        $this->entityManager->expects($this->never())
            ->method('find');

        $result = $this->objectManager->fetch(null);
        $this->assertNull($result);
        $result = $this->objectManager->fetch('a string');
        $this->assertNull($result);
        $result = $this->objectManager->fetch('');
        $this->assertNull($result);
        $result = $this->objectManager->fetch(false);
        $this->assertNull($result);
    }

    public function testFetchLoadsFromAdapter()
    {
        $id = 24;
        $object = new \stdClass();
        $entityType = "RandomTestEntityType";

        $this->objectAdapter->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue($entityType));

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with($entityType, $id)
            ->will($this->returnValue($object));

        $result = $this->objectManager->fetch($id);

        $this->assertEquals($object, $result);
    }

    /*
    public function testFetchListWithNoSearch()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->once())
            ->method('select')
            ->with('c')
            ->will($this->returnSelf());

        $qb->expects($this->once())
            ->method('from')
            ->with('SclBusiness\Entity\Accounting\Customer', 'c');

        $this->entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $this->objectManager->fetchList();
    }
    */

    public function testSave()
    {
        $customer = new Customer();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($customer);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->objectManager->save($customer);
    }

    public function testDelete()
    {
         $customer = new Customer();

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($customer);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->objectManager->delete($customer);
    }
}

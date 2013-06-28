<?php

namespace SclZfSearchableTests\SearchInfo;

use SclZfSearchable\SearchInfo\SessionSearchInfo;

/**
 * Unit tests from {@see SessionSearchInfo}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 * @todo   Work out how to test access to a session container.
 */
class SessionSearchInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The instance to be tested.
     *
     * @var mixed
     */
    protected $searchInfo;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    public function setUp()
    {
        $this->searchInfo = new SessionSearchInfo();
    }

    /**
     * Test that when setOrder is called with a bad string an exception is thrown.
     *
     * @dataProvider      badColumnNames
     * @covers            SclZfSearchable\SearchInfo\SessionSearchInfo::setOrderBy
     * @expectedException SclZfSearchable\Exception\DomainException
     *
     * @return void
     */
    public function testOrderByWithBadCharacters($badString)
    {
        $this->searchInfo->setOrderBy($badString);
    }

    public function badColumnNames()
    {
        return array(
                array('!'),
                array('"'),
                array('£'),
                array('$'),
                array('%'),
                array('^'),
                array('*'),
                array('('),
                array(')'),
                array('+'),
                array('~'),
                array('#'),
                array('\''),
                array('\\'),
        );
    }

    /**
     * Test that an exception gets thrown when setOrder is called with an illegal value.
     *
     * @covers            SclZfSearchable\SearchInfo\SessionSearchInfo::setOrder
     * @expectedException SclZfSearchable\Exception\DomainException
     *
     * @return void
     */
    public function testSetOrderWithBadValue()
    {
        $this->searchInfo->setOrder('not-desc-or-asc');
    }
}

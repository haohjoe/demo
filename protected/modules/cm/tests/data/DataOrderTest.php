<?php

/**
 * DataOrder test case.
 */
class DataOrderTest extends PTest
{
    private $solution = 'cc';
    private $appName = 'CameraCircle';
    
    private $fakeDaoOrder;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fakeDaoOrder = $this->getMock('DaoOrder', array(
            'getInfoByAoId'
        ), array($this->solution, $this->appName));
        Factory::set('DaoOrder', $this->fakeDaoOrder);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->fakeDaoOrder = null;
        parent::tearDown();
    }

    public function testGetByAoId()
    {
        $arrRet = array();
        $uid = new MongoId();
        $uid = $uid->__toString();
        $aoId = 'abc_xxx';
        $this->fakeDaoOrder->expects($this->once())
            ->method('getInfoByAoId')
            ->with($uid, $aoId)
            ->will($this->returnValue($arrRet));
        
        $objDataOrder = new DataOrder($this->solution, $this->appName);
        $actual = $objDataOrder->getByAoId($uid, $aoId);
        $expected = $arrRet;
        
        $this->assertEquals($expected, $actual);
    }
}

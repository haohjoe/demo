<?php

/**
 * DataUserCpoint test case.
 */
class DataUserCpointTest extends PTest
{
    private $solution = 'cc';
    
    private $fakeDaoUserCpoint;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fakeDaoUserCpoint = $this->getMock('DaoUserCpoint', array(
            'getById',
            'addCpoint'
        ), array($this->solution));
        Factory::set('DaoUserCpoint', $this->fakeDaoUserCpoint);
    }

    protected function tearDown()
    {
        $this->fakeDaoUserCpoint = null;
        parent::tearDown();
    }

    public function testGetByUid()
    {
        $strUid = '0cd9fc534df54b4f2207b7b6';
        
        $arrRet = array(
            'id' => '0cd9fc534df54b4f2207b7b6',
            'u_time' => 1418699713,
            'cpoint' => 0.1
        );
        
        $this->fakeDaoUserCpoint->expects($this->any())
            ->method('getById')
            ->with($this->equalTo($strUid))
            ->will($this->returnValue($arrRet));
        
        $objDataUserCpoint = new DataUserCpoint($this->solution);
        $actual = $objDataUserCpoint->getByUid($strUid);
        $expected = $arrRet;
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetByUid_Fail()
    {
        $strUid = '0cd9fc534df54b4f2207b7b6';
        
        $this->fakeDaoUserCpoint->expects($this->any())
            ->method('getById')
            ->with($this->equalTo($strUid))
            ->will($this->returnValue(false));
        
        $objDataUserCpoint = new DataUserCpoint($this->solution);
        $actual = $objDataUserCpoint->getByUid($strUid);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }

    public function testAddCpoint()
    {
        $strUid = '0cd9fc534df54b4f2207b7b6';
        $addCpoint = 0.1;
        
        $arrRet = true;
        $this->fakeDaoUserCpoint->expects($this->any())
            ->method('addCpoint')
            ->with($this->equalTo($strUid))
            ->will($this->returnValue(true));
        
        $objDataUserCpoint = new DataUserCpoint($this->solution);
        $actual = $objDataUserCpoint->addCpoint($strUid, $addCpoint);
        $expected = true;
        
        $this->assertEquals($expected, $actual);
    }

    public function testAddCpoint_Fail()
    {
        $strUid = '0cd9fc534df54b4f2207b7b6';
        $addCpoint = 0.1;
        
        $this->fakeDaoUserCpoint->expects($this->any())
            ->method('addCpoint')
            ->with($this->equalTo($strUid), $this->equalTo($addCpoint))
            ->will($this->returnValue(false));
        
        $objDataUserCpoint = new DataUserCpoint($this->solution);
        $actual = $objDataUserCpoint->addCpoint($strUid, $addCpoint);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }
}

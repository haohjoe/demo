<?php

class UseCpointLogicTest extends PTest
{
    private $solution = 'cc';
    private $appName = 'CameraCircle';

    protected function setUp()
    {
        if (defined('TEST') == false) {
            define('TEST', 1);
        }
    }

    public function testConsume()
    {
        $mUid = new MongoId();
        $data = array(
            'uid' => $mUid->__toString(),
            'cpoint' => 1.0,
            'orderId' => 'xxx'
        );
        $aoId = DataBase::makeAoid($this->appName, $data['orderId']);
        $md1 = array(
            'cpoint' => 1
        );
        $mock = $this->getMock('UseCpointLogic', array(
            'addAccountChangeLog'
        ), array($this->solution, $this->appName));
        $mock->expects($this->once())
            ->method('addAccountChangeLog')
            ->will($this->returnValue(true));

        $mockDUCV2 = $this->getMock('DataUserCpointV2', array(
            'getByUid'
        ), array($this->solution));
        $mockDUCV2->expects($this->once())
            ->method('getByUid')
            ->with($data['uid'])
            ->will($this->returnValue($md1));
        $mock->objDataUserCpointV2 = $mockDUCV2;
        $mockDO = $this->getMock('DataOrder', array(
            'getByAoId'
        ), array($this->solution, $this->appName));
        $mockDO->expects($this->once())
            ->method('getByAoId')
            ->with($data['uid'], $aoId)
            ->will($this->returnValue(array()));
        $mock->objDataOrder = $mockDO;
        $mockDB = $this->getMock('DataBase', array(
            'consumeCpoint'
        ), array($this->solution, $this->appName));
        $mockDB->expects($this->once())
            ->method('consumeCpoint')
            ->with($data['uid'], $data['cpoint'], $aoId, $data['orderId'], json_encode($data))
            ->will($this->returnValue(true));
        $mock->objDataBase = $mockDB;

        $rst = $mock->consume($data);
        $actual = array(
            'id' => "CameraCircle_xxx",
            'cpoint' => 0
        );
        $this->assertEquals($actual, $rst);
    }

    public function testRevoke()
    {
        $mUid = new MongoId();
        $data = array(
            'uid' => $mUid->__toString(),
            'cpoint' => 1.0,
            'orderId' => 'xxx'
        );
        $aoId = DataBase::makeAoid($this->appName, $data['orderId']);
        $md1 = array(
            'cpoint' => 1
        );
        $mOrder = array(
            'uid' => $data['uid'],
            'status' => DaoOrder::STATUS_PAID,
            'cpoint' => 1.0
        );
        $mock = $this->getMock('UseCpointLogic', array(
            'addAccountChangeLog'
        ), array($this->solution, $this->appName));
        $mock->expects($this->once())
            ->method('addAccountChangeLog')
            ->will($this->returnValue(true));

        $mockDUCV2 = $this->getMock('DataUserCpointV2', array(
            'getByUid'
        ), array($this->solution));
        $mockDUCV2->expects($this->once())
            ->method('getByUid')
            ->with($data['uid'])
            ->will($this->returnValue($md1));
        $mock->objDataUserCpointV2 = $mockDUCV2;
        $mockDO = $this->getMock('DataOrder', array(
            'getByAoId'
        ), array($this->solution, $this->appName));
        $mockDO->expects($this->once())
            ->method('getByAoId')
            ->with($data['uid'], $aoId)
            ->will($this->returnValue($mOrder));
        $mock->objDataOrder = $mockDO;
        $mockDB = $this->getMock('DataBase', array(
            'revokeCpoint'
        ), array($this->solution, $this->appName));
        $mockDB->expects($this->once())
            ->method('revokeCpoint')
            ->with($data['uid'], $data['cpoint'], $aoId, json_encode($data))
            ->will($this->returnValue(true));
        $mock->objDataBase = $mockDB;

        $rst = $mock->revoke($data);
        $actual =  "CameraCircle_xxx";
        $this->assertEquals($actual, $rst);
    }
}

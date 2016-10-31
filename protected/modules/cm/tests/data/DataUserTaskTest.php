<?php

/**
 * DataUserTask test case.
 */
class DataUserTaskTest extends PTest
{
    private $solution = 'cc';
    
    private $fakeDaoUserTask;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fakeDaoUserTask = $this->getMock('DaoUserTask', array(
            'getByIds',
            'insert',
            'incScoreAndCpoint',
            'updateData'
        ), array($this->solution));
        
        Factory::set('DaoUserTask', $this->fakeDaoUserTask);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->fakeDaoUserTask = null;
        parent::tearDown();
    }

    public function testGetUserTaskByTaskIds()
    {
        $uid = '532a5aa788ec99d578386bd2';
        $arrTaskIds = array(
            1
        );
        
        $ids = array(
            '532a5aa788ec99d578386bd2_1'
        );
        $data = array(
            '532a5aa788ec99d578386bd2_1' => array(
                'id' => '532a5aa788ec99d578386bd2_1',
                't_id' => 1
            )
        );
        $this->fakeDaoUserTask->expects($this->any())
            ->method('getByIds')
            ->with($this->equalTo($ids))
            ->will($this->returnValue($data));
        
        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->getUserTaskByTaskIds($uid, $arrTaskIds);
        $expected = array(
            1 => array(
                'id' => '532a5aa788ec99d578386bd2_1',
                't_id' => 1
            )
        );
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetUserTaskByTaskIds_Fail()
    {
        $uid = '0f064d52469523c46ab99124';
        $arrTaskIds = array(
            4
        );
        $ids = array(
            '0f064d52469523c46ab99124_4'
        );
        
        $this->fakeDaoUserTask->expects($this->any())
            ->method('getByIds')
            ->with($this->equalTo($ids))
            ->will($this->returnValue(false));
        
        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->getUserTaskByTaskIds($uid, $arrTaskIds);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }

    public function testInsert()
    {
        $arrNewUserTask = array(
            'uid' => '50f75b8bd759a0a120000059',
            't_id' => 1,
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );
        
        $this->fakeDaoUserTask->expects($this->any())
            ->method('insert')
            ->with($this->equalTo($arrNewUserTask))
            ->will($this->returnValue(true));
        
        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->insert($arrNewUserTask);
        $expected = true;
        
        $this->assertEquals($expected, $actual);
    }

    public function testInsert_Fail()
    {
        $arrNewUserTask = array(
            'uid' => '50f75b8bd759a0a120000059',
            't_id' => 1,
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );
        
        $this->fakeDaoUserTask->expects($this->any())
            ->method('insert')
            ->with($this->equalTo($arrNewUserTask))
            ->will($this->returnValue(false));
        
        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->insert($arrNewUserTask);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateData()
    {
        $arrNewUserTask = array(
            'uid' => '50f75b8bd759a0a120000059',
            't_id' => 1,
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );

        $this->fakeDaoUserTask->expects($this->any())
            ->method('updateData')
            ->with($this->equalTo($arrNewUserTask))
            ->will($this->returnValue(true));

        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->updateData($arrNewUserTask);
        $expected = true;

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateData_Fail()
    {
        $arrNewUserTask = array(
            'uid' => '50f75b8bd759a0a120000059',
            't_id' => 1,
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );

        $this->fakeDaoUserTask->expects($this->any())
            ->method('updateData')
            ->with($this->equalTo($arrNewUserTask))
            ->will($this->returnValue(false));

        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->updateData($arrNewUserTask);
        $expected = false;

        $this->assertEquals($expected, $actual);
    }

    public function testIncScoreAndCpoint()
    {
        $arrNewUserTask = array(
            'id' => '0f064d52469523c46ab99124',
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );
        
        $this->fakeDaoUserTask->expects($this->any())
            ->method('incScoreAndCpoint')
            ->with($this->equalTo($arrNewUserTask))
            ->will($this->returnValue(true));
        
        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->incScoreAndCpoint($arrNewUserTask);
        $expected = true;
        
        $this->assertEquals($expected, $actual);
    }

    public function testIncScoreAndCpoint_Fail()
    {
        $arrNewUserTask = array(
            'id' => '0f064d52469523c46ab99124',
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );
        
        $this->fakeDaoUserTask->expects($this->any())
            ->method('incScoreAndCpoint')
            ->with($this->equalTo($arrNewUserTask))
            ->will($this->returnValue(false));
        
        $objDataUserTask = new DataUserTask($this->solution);
        $actual = $objDataUserTask->incScoreAndCpoint($arrNewUserTask);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }
}

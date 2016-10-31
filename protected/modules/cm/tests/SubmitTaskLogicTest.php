<?php

class SubmitTaskLogicTest extends PTest
{
    private $solution = 'cc';
    private $appName = 'CameraCircle';
    
    protected function setUp()
    {
        if (defined('TEST') == false) {
            define('TEST', 'TEST');
        }
    }

    /**
     * 
     */
    public function testExecute()
    {
        $uid = new MongoId();
        $now = time();
        $arrParams = array(
            'uid' => $uid->__toString(),
            'op' => 'bindWeixin',
            'target' => '',
            'times' => 1
        );
        $cnfVal = array(
            1 => array(
                'id' => 1,
                'name' => 'xxx',
                'desc' => 'xxx',
                'ops' => array(
                    'bindWeixin'
                ),
                'score' => 50,
                'cpoint' => 60,
                'step_score' => array(),
                'step_cpoint' => array(),
                'status' => 1,
                'rule' => array(
                    'ref_class' => 'RuleBase',
                    's_time' => '',
                    'e_time' => '',
                    's_grade' => - 1,
                    'e_grade' => - 1,
                    'batch' => 0,
                    'repeat' => 0,
                    'r_flag' => 0,
                    'r_ratio' => 0,
                    'r_count' => 0,
                    'r_score' => 0,
                    'r_cpoint' => 0,
                    'r_filter' => 0,
                    'r_step' => 0,
                )
            ),
        );
        $tids = array_keys($cnfVal);
        $uTasksVal = array(

        );
        $uScoresVal = array(
            'score' => 100,
        );
        $uCrtGrade = 1;

        $completedTasks = array(
            1 => array(
                'uid' => $uid->__toString(),
                't_id' => 1,
                'u_time' => $now,
                'c_time' => $now,
                'actions' => array(),
                'cpoint' => 60,
                'score' => 50,
            )
        );
        $mock = $this->getMock('SubmitTaskLogic', array(
            'addScoreAndCpoint',
            'recordUserCompentedTask'
        ), array($this->solution, $this->appName));
        $mock->expects($this->once())
            ->method('addScoreAndCpoint')
            ->will($this->returnValue(true));
        $mock->expects($this->once())
            ->method('recordUserCompentedTask')
            ->will($this->returnValue(true));

        $mockCnf = $this->getMock('DataCnf', array('mapOp2Tasks'), array($this->solution));
        $mockCnf->expects($this->once())
                ->method('mapOp2Tasks')
                ->with($arrParams['op'])
                ->will($this->returnValue($cnfVal));
        $mockDUT = $this->getMock('DataUserTask', array('getUserTaskByTaskIds', 'incScoreAndCpoint'), array($this->solution));
        $mockDUT->expects($this->any())
                ->method('incScoreAndCpoint')
                ->with($arrParams['uid'], $tids)
                ->will($this->returnValue($uTasksVal));
        $mockDUT->expects($this->once())
                ->method('getUserTaskByTaskIds')
                ->with($arrParams['uid'], $tids)
                ->will($this->returnValue($uTasksVal));
        $mockDUS = $this->getMock('DataUserScore', array('getByUid', 'getGradeByScore'), array($this->solution));
        $mockDUS->expects($this->once())
                ->method('getByUid')
                ->with($arrParams['uid'])
                ->will($this->returnValue($uScoresVal));
        $mockDUS->expects($this->any())
                ->method('getGradeByScore')
                ->will($this->returnValue($uCrtGrade));
        $mockRE = $this->getMock('RuleEngine', array('run'), array($this->solution));
        $mockRE->expects($this->once())
                ->method('run')
                ->will($this->returnValue($completedTasks));
        $mockDUC = $this->getMock('DataUserCpoint', array('getByUid'), array($this->solution));
        $mockDUC->expects($this->once())
                ->method('getByUid')
                ->will($this->returnValue(array()));
        $mockDUCV2 = $this->getMock('DataUserCpointV2', array('getByUid'), array($this->solution));
        $mockDUCV2->expects($this->once())
                ->method('getByUid')
                ->will($this->returnValue(array()));

        $mock->objDataCnf = $mockCnf;
        $mock->objDataUserTask = $mockDUT;
        $mock->objDataUserScore = $mockDUS;
        $mock->objDataUserCpoint = $mockDUC;
        $mock->objDataUserCpointV2 = $mockDUCV2;
        $mock->objRuleEngine = $mockRE;

        $rst = $mock->execute($arrParams);

        $this->assertEquals(50, $rst['score']);
        $this->assertEquals(60, $rst['cpoint']);
        $this->assertEquals(0, $rst['code']);
    }
}

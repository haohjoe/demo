<?php

/**
 * DataUserScore test case.
 */
class DataUserScoreTest extends PTest
{
    private $solution = 'cc';

    private $fakeDaoUserScore;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fakeDaoUserScore = $this->getMock('DaoUserScore', array(
            'getById',
            'addScore'
        ), array($this->solution));
        Factory::set('DaoUserScore', $this->fakeDaoUserScore);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->fakeDaoUserScore = null;
        parent::tearDown();
    }

    public function testGetByUid()
    {
        $strUid = '532a5aa788ec99d578386bd2';
        
        $arrRet = array(
            'uid' => '532a5aa788ec99d578386bd2',
            'score' => 0.1,
            'grades' => array(
                "grade" => 0,
                "t_id" => 1,
                "c_time" => 1418130436
            ),
            'flag' => 1,
            'c_time' => 1418379383
        );
        $this->fakeDaoUserScore->expects($this->any())
            ->method('getById')
            ->with($this->equalTo($strUid))
            ->will($this->returnValue($arrRet));
        
        $objDataUserScore = new DataUserScore($this->solution);
        $actual = $objDataUserScore->getByUid($strUid);
        $expected = $arrRet;
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetByUid_Fail()
    {
        $strUid = '532a5aa788ec99d578386bd2';
        
        $this->fakeDaoUserScore->expects($this->any())
            ->method('getById')
            ->with($this->equalTo($strUid))
            ->will($this->returnValue(false));
        
        $objDataUserScore = new DataUserScore($this->solution);
        $actual = $objDataUserScore->getByUid($strUid);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetGradeByScore()
    {
        $intUserScore = 20;
        
        $this->fakeDaoUserScore->expects($this->any())
            ->method('')
            ->with($this->equalTo(Yii::app()->params['grade']), $this->equalTo($intUserScore))
            ->will($this->returnValue(0));
        
        $objDataUserScore = new DataUserScore($this->solution);
        $actual = $objDataUserScore->getGradeByScore($intUserScore);
        
        $this->assertEquals(1, $actual);
    }

    public function testGetGradeByScore_Fail()
    {
        $intUserScore = 20;
        
        $this->fakeDaoUserScore->expects($this->any())
            ->method('')
            ->with($this->equalTo(Yii::app()->params['grade']), $this->equalTo($intUserScore))
            ->will($this->returnValue(1));
        
        $objDataUserScore = new DataUserScore($this->solution);
        $actual = $objDataUserScore->getGradeByScore($intUserScore);
        
        $this->assertEquals(1, $actual);
    }

    public function testAddScore()
    {
        $strUid = '532a5aa788ec99d578386bd2';
        $addScore = 0.1;
        $arrNewGrades = 0;
        
        $arrRet = true;
        $this->fakeDaoUserScore->expects($this->any())
            ->method('addScore')
            ->with($this->equalTo($strUid), $this->equalTo($addScore), $this->equalTo($arrNewGrades))
            ->will($this->returnValue(true));
        
        $objDataUserScore = new DataUserScore($this->solution);
        $actual = $objDataUserScore->addScore($strUid, $addScore, $arrNewGrades);
        $expected = true;
        
        $this->assertEquals($expected, $actual);
    }

    public function testAddScore_Fail()
    {
        $strUid = '532a5aa788ec99d578386bd2';
        $addScore = 0.1;
        $arrNewGrades = 0;
        
        $this->fakeDaoUserScore->expects($this->any())
            ->method('addScore')
            ->with($this->equalTo($strUid), $this->equalTo($addScore), $this->equalTo($arrNewGrades))
            ->will($this->returnValue(false));
        
        $objDataUserScore = new DataUserScore($this->solution);
        $actual = $objDataUserScore->addScore($strUid, $addScore, $arrNewGrades);
        $expected = false;
        
        $this->assertEquals($expected, $actual);
    }
}

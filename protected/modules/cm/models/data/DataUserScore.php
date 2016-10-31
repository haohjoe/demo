<?php

/**
 * DataUserScore class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class DataUserScore
{
    public $solution;
    public $objDaoUserScore = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoUserScore = Factory::create('DaoUserScore', array($this->solution));
    }
    
    /**
     * 获取用户经验.
     * @param string $strUid
     * @return boolean|array
     */
    public function getByUid($uid)
    {
        $arrRst = $this->objDaoUserScore->getById($uid);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserScore->getById fail'
            );
            LogWrapper::warning($arrLog);
            
            return false;
        }
        
        return $arrRst;
    }
    
    /**
     * 获取多个用户的经验值.
     * @param $uids array 用户uid
     * @return mixed
     */
    public function getByUids(Array $uids)
    {
        if (empty($uids)) {
            return array();
        }
        $arrRst = $this->objDaoUserScore->getByIds($uids);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserScore->getByIds fail'
            );
            LogWrapper::warning($arrLog);
    
            return false;
        }
    
        return $arrRst;
    }
    
    /**
     * 排序 获取用户经验信息.
     * @param $page int 当前页
     * @param $limit int 每页显示数
     * @param $sort int 排序方式
     * @return mixed
     */
    public function getSortList($page, $limit, $sort)
    {
        $skip = max(0, ($page - 1) * $limit);
        $arrRst = $this->objDaoUserScore->getSortList($skip, $limit, $sort);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserScore->getSortList fail'
            );
            LogWrapper::warning($arrLog);
    
            return false;
        }
    
        return $arrRst;
    }
    
    /**
     * 某等级下对score进行排序.
     * @param $intGrade int 等级
     * @param $page int 当前页
     * @param $limit int 每页显示数
     * @param $sort int 排序方式
     * @return mixed
     */
    public function getSortListByGrade($intGrade, $page, $limit, $sort)
    {
        $skip = max(0, ($page - 1) * $limit);
        // 获取经验值区间，如果结束值没有（无穷值）会返回null
        $section = $this->getScoreByGrade($intGrade);
        $arrRst = $this->objDaoUserScore->getSortListByScoreSection($section[0], $section[1], $skip, $limit, $sort);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserScore->getSortListByScoreSection fail'
            );
            LogWrapper::warning($arrLog);
        
            return false;
        }
        
        return $arrRst;
    }
    
    /**
     * 通过经验值计算用户的等级.
     * 
     * @param int $intUserScore 
     * @return int
     */
    public function getGradeByScore($intUserScore)
    {
        $arrGradeInfo = Yii::app()->params['grade'];
        $intUserGrade = 0;
        foreach ($arrGradeInfo as $intGrade => $intScore) {
            if ($intGrade > $intUserGrade && $intUserScore >= $intScore) {
                $intUserGrade = $intGrade;
            }
        }
        
        return $intUserGrade;
    }
    
    /**
     * 根据等级获取经验值区间
     * @param $intGrade int 等级
     * @return array
     */
    public function getScoreByGrade($intGrade)
    {
        $p = Yii::app()->params['grade'];
        $s = $p[$intGrade];
        $e = isset($p[$intGrade + 1]) ? $p[$intGrade + 1] : null;
        
        return array($s, $e);
    }
    
    /**
     * 增加用户经验
     * @param string $strUid
     * @param int $addScore
     * @param array $arrNewGrades
     * @return boolean
     */
    public function addScore($strUid, $addScore, $arrNewGrades)
    {
        $arrRst = $this->objDaoUserScore->addScore($strUid, $addScore, $arrNewGrades);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserScore->addScore fail',
                'uid' => $strUid,
                'score' => $addScore
            );
            LogWrapper::warning($arrLog);
            
            return false;
        }
        
        return true;
    }
}

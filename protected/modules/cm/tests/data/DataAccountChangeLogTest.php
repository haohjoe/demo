<?php

/**
 * DataAccountChangeLogTest test case.
 */
class DataAccountChangeLogTest extends PTest
{
    private $solution = 'cc';
    
    private $fakeDao;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fakeDao = $this->getMock('DaoAccountChangeLog', array(
            'getByIds',
            'insert',
            'getList'
        ), array($this->solution));
        
        Factory::set('DaoAccountChangeLog', $this->fakeDao);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->fakeDao = null;
        parent::tearDown();
    }

    public function testGetByIds()
    {
        $ids = array(
            '532a5aa788ec99d578386bd2',
            '532a5aa788ec99d578386bd3'
        );
        $data = array(
            '532a5aa788ec99d578386bd2' => array(
                'id' => '532a5aa788ec99d578386bd2',
            ),
            '532a5aa788ec99d578386bd3' => array(
                'id' => '532a5aa788ec99d578386bd3',
            )
        );
        $this->fakeDao->expects($this->once())
            ->method('getByIds')
            ->with($this->equalTo($ids))
            ->will($this->returnValue($data));
        
        $dacl = new DataAccountChangeLog($this->solution);
        $rst = $dacl->getByIds($ids);
        $this->assertEquals($data, $rst);
    }

    public function testGetList()
    {
        $args = array();
        $data = array(
            'ff064d52469523c46ab99125' => array()
        );
        
        $this->fakeDao->expects($this->any())
            ->method('getList')
            ->with($args)
            ->will($this->returnValue($data));
        
        $dacl = new DataAccountChangeLog($this->solution);
        $rst = $dacl->getList($args);
        
        $this->assertEquals($data, $rst);
    }

    public function testInsert()
    {
        $data = array();
        
        $this->fakeDao->expects($this->any())
            ->method('insert')
            ->with($this->equalTo($data))
            ->will($this->returnValue(true));
        
        $dacl = new DataAccountChangeLog($this->solution);
        $rst = $dacl->insert($data);
        
        $this->assertEquals(true, $rst);
    }
}

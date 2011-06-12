<?php
require_once 'phing/BuildFileTest.php';

class rSTTaskTest extends BuildFileTest 
{ 
    public function setUp() 
    { 
        $this->configureProject(
            PHING_TEST_BASE . '/build.xml'
        );
    }


    public function testSingleFileParameterFile()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertTrue(
            file_exists(dirname(__FILE__) . '/single.html'),
            'single.html has not been created'
        );
        unlink(dirname(__FILE__) . '/single.html');
        //$this->assertInLogs('Property ${version} => 1.0.1');
    }

    public function testSingleFileParameterFileNoExt()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertTrue(
            file_exists(dirname(__FILE__) . '/single-no-ext.html'),
            'single-no-ext.html has not been created'
        );
        unlink(dirname(__FILE__) . '/single-no-ext.html');
        //$this->assertInLogs('Property ${version} => 1.0.1');
    }

}

?>
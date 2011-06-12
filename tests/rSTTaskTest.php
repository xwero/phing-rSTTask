<?php
require_once 'phing/BuildFileTest.php';

class rSTTaskTest extends BuildFileTest 
{ 
    public function setUp() 
    { 
        $this->configureProject(
            PHING_TEST_BASE . '/build.xml'
        );
        //$this->assertInLogs('Property ${version} => 1.0.1');
    }



    /**
     * Checks if a given file has been created and unlinks it afterwards.
     *
     * @param string $file relative file path
     *
     * @return void
     */
    protected function assertFileCreated($file)
    {
        $this->assertTrue(
            file_exists(dirname(__FILE__) . '/' . $file),
            $file . ' has not been created'
        );
        unlink(dirname(__FILE__) . '/' . $file);
    }


    public function testSingleFileParameterFile()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('single.html');
    }

    public function testSingleFileParameterFileNoExt()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('single-no-ext.html');
    }

    public function testSingleFileParameterFileFormat()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('single.3');
    }

    public function testSingleFileInvalidParameterFormat()
    {
        $this->expectBuildExceptionContaining(
            __FUNCTION__, 'Invalid parameter',
            'Invalid output format "foo", allowed are'
        );
    }

    public function testSingleFileParameterFileFormatTarget()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('single-target.html');
    }

    public function testBrokenFile()
    {
        $this->expectBuildExceptionContaining(
            __FUNCTION__, 'Broken file',
            'Rendering rST failed'
        );
        $this->assertFileCreated('broken.html');
    }
}

?>
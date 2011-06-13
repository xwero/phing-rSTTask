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
        $this->assertFileCreated('files/single.html');
    }

    public function testSingleFileParameterFileNoExt()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single-no-ext.html');
    }

    public function testSingleFileParameterFileFormat()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single.3');
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
        $this->assertFileCreated('files/single-target.html');
    }

    public function testBrokenFile()
    {
        $this->expectBuildExceptionContaining(
            __FUNCTION__, 'Broken file',
            'Rendering rST failed'
        );
        $this->assertInLogs(
            'broken.rst:2: (WARNING/2)'
            . ' Bullet list ends without a blank line; unexpected unindent.'
        );
        $this->assertFileCreated('files/broken.html');
    }

    public function testMultiple()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single.html');
        $this->assertFileCreated('files/two.html');
    }

    public function testMultipleDir()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single.html');
        $this->assertFileCreated('files/two.html');
    }

    public function testMultipleDirWildcard()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single.html');
    }
}

?>
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
        $this->assertFileExists(
            dirname(__FILE__) . '/' . $file,
            $file . ' has not been created'
        );
        unlink(dirname(__FILE__) . '/' . $file);
    }



    /**
     * @expectedException BuildException
     * @expectedExceptionMessage "rst2doesnotexist" not found. Install python-docutils.
     */
    public function testGetToolPathFail()
    {
        $rt = new rSTTask();
        $ref = new ReflectionClass($rt);
        $method = $ref->getMethod('getToolPath');
        $method->setAccessible(true);
        $method->invoke($rt, 'doesnotexist');
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

    public function testMissingFiles()
    {
        $this->expectBuildExceptionContaining(
            __FUNCTION__, 'Missing attributes/tags',
            '"file" attribute or "fileset" subtag required'
        );
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


    public function testMultipleMapper()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single.my.html');
        $this->assertFileCreated('files/two.my.html');
    }


    public function testFilterChain()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileExists('files/filterchain.html');
        $cont = file_get_contents(
            dirname(__FILE__) . '/files/filterchain.html'
        );
        $this->assertContains('This is a bar.', $cont);
        unlink(dirname(__FILE__) . '/files/filterchain.html');
    }



    public function testCustomParameter()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileExists('files/single.html');
        $file = dirname(__FILE__) . '/files/single.html';
        $cont = file_get_contents($file);
        $this->assertContains('this is a custom css file', $cont);
        $this->assertContains('#FF8000', $cont);
        unlink($file);
    }
}

?>
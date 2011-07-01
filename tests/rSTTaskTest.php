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

    /**
     * Get the tool path previously set with setToolPath()
     */
    public function testGetToolPathCustom()
    {
        $rt = new rSTTask();
        $rt->setToolPath('true');//mostly /bin/true on unix
        $ref = new ReflectionClass($rt);
        $method = $ref->getMethod('getToolPath');
        $method->setAccessible(true);
        $this->assertContains('/true', $method->invoke($rt, 'foo'));
    }



    /**
     * @expectedException BuildException
     * @expectedExceptionMessage Tool does not exist. Path:
     */
    public function testSetToolpathNotExisting()
    {
        $rt = new rSTTask();
        $rt->setToolPath('doesnotandwillneverexist');
    }

    /**
     * @expectedException BuildException
     * @expectedExceptionMessage Tool not executable. Path:
     */
    public function testSetToolpathNonExecutable()
    {
        $rt = new rSTTask();
        $rt->setToolPath(__FILE__);
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

    public function testSingleFileParameterFileFormatDestination()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/single-destination.html');
    }

    public function testParameterUptodate()
    {
        $this->executeTarget(__FUNCTION__);
        $file = dirname(__FILE__) . '/files/single.html';
        $this->assertFileExists($file);
        $this->assertEquals(
            0, filesize($file),
            'File size is not 0, which it should have been when'
            . ' rendering was skipped'
        );
        unlink($file);
    }

    public function testDirectoryCreation()
    {
        $this->executeTarget(__FUNCTION__);
        $this->assertFileCreated('files/a/b/c/single.html');
        rmdir(dirname(__FILE__) . '/files/a/b/c');
        rmdir(dirname(__FILE__) . '/files/a/b');
        rmdir(dirname(__FILE__) . '/files/a');
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

    /**
     * @expectedException BuildException
     * @expectedExceptionMessage No filename mapper found for "./files/single.rst"
     */
    public function testNotMatchingMapper()
    {
        $this->executeTarget(__FUNCTION__);
    }


    public function testFilterChain()
    {
        $this->executeTarget(__FUNCTION__);
        $file = dirname(__FILE__) . '/files/filterchain.html';
        $this->assertFileExists($file);
        $cont = file_get_contents($file);
        $this->assertContains('This is a bar.', $cont);
        unlink($file);
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
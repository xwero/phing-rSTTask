<?php
/**
 * reStructuredText rendering task for Phing, the PHP build tool.
 *
 * PHP version 5
 *
 * @category   Tasks
 * @package    Phing
 * @subpackage rST
 * @author     Christian Weiske <cweiske@cweiske.de>
 * @license    LGPL v3 or later http://www.gnu.org/licenses/lgpl.html
 * @link       https://gitorious.org/phing/rsttask
 */
require_once 'phing/Task.php';
require_once 'phing/util/FileUtils.php';
require_once 'System.php';

/**
 * reStructuredText rendering task for Phing, the PHP build tool.
 *
 * PHP version 5
 *
 * @category   Tasks
 * @package    Phing
 * @subpackage rST
 * @author     Christian Weiske <cweiske@cweiske.de>
 * @license    LGPL v3 or later http://www.gnu.org/licenses/lgpl.html
 * @link       https://gitorious.org/phing/rsttask
 */
class rSTTask extends Task
{
    /**
     * @var string Taskname for logger
     */
    protected $taskName = 'rST';

    /**
     * Result format, defaults to "html".
     * @see $supportedFormats for all possible options
     *
     * @var string
     */
    protected $format = 'html';

    /**
     * Array of supported output formats
     *
     * @var array
     * @see $format
     * @see $targetExt
     */
    protected static $supportedFormats = array(
        'html', 'latex', 'man', 'odt', 's5', 'xml'
    );

    /**
     * Maps formats to file extensions
     *
     * @var array
     */
    protected static $targetExt = array(
        'html'  => 'html',
        'latex' => 'tex',
        'man'   => '3',
        'odt'   => 'odt',
        's5'    => 'html',
        'xml'   => 'xml',
    );

    /**
     * Input file in rST format.
     * Required
     *
     * @var string
     */
    protected $file = null;

    /**
     * Output file. May be omitted.
     *
     * @internal
     * We need to use $targetFile because Task itself defines $target
     *
     * @var string
     */
    protected $targetFile = null;

    protected $filesets      = array(); // all fileset objects assigned to this task
    protected $mapperElement = null;

    /**
     * all filterchains objects assigned to this task
     *
     * @var array
     */
    protected $filterChains = array();

    /**
     * mode to create directories with
     *
     * @var integer
     */
    protected $mode = 0755;



    /**
     * The setter for the attribute "file"
     *
     * @param string $file Path of file to render
     *
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
    }



    /**
     * The setter for the attribute "format"
     *
     * @param string $format Output format
     *
     * @return void
     *
     * @throws BuildException When the format is not supported
     */
    public function setFormat($format)
    {
        if (!in_array($format, self::$supportedFormats)) {
            throw new BuildException(
                sprintf(
                    'Invalid output format "%s", allowed are: %s',
                    $format,
                    implode(', ', self::$supportedFormats)
                )
            );
        }
        $this->format = $format;
    }



    /**
     * The setter for the attribute "target"
     *
     * @param string $targetFile Output file
     *
     * @return void
     */
    public function setTarget($targetFile)
    {
        $this->targetFile = $targetFile;
    }



    /**
     * The main entry point method.
     *
     * @return void
     */
    public function main()
    {
        $tool = $this->getToolPath();
        if (count($this->filterChains)) {
            $this->fileUtils = new FileUtils();
        }

        if ($this->file != '') {
            $file   = $this->file;
            $target = $this->getTargetFile($file, $this->targetFile);
            $this->render($tool, $file, $target);
            return;
        }

        if (!count($this->filesets)) {
            throw new BuildException(
                '"file" attribute or "fileset" subtag required'
            );
        }

        // process filesets
        $mapper = null;
        if ($this->mapperElement !== null) {
            $mapper = $this->mapperElement->getImplementation();
        }

        $project = $this->getProject();
        foreach ($this->filesets as $fs) {
            $ds = $fs->getDirectoryScanner($project);
            $fromDir  = $fs->getDir($project);
            $srcFiles = $ds->getIncludedFiles();

            foreach ($srcFiles as $src) {
                $file  = new PhingFile($fromDir, $src);
                if ($mapper !== null) {
                    $target = reset($mapper->main($file));
                } else {
                    $target = $this->getTargetFile($file);
                }
                $this->render($tool, $file, $target);
            }
        }
    }



    /**
     * Renders a single file and applies filters on it
     *
     * @param string $tool   conversion tool to use
     * @param string $source rST source file
     * @param string $target target file name
     *
     * @return void
     */
    protected function render($tool, $source, $target)
    {
        if (count($this->filterChains) == 0) {
            return $this->renderFile($tool, $source, $target);
        }

        $tmpTarget = tempnam(sys_get_temp_dir(), 'rST-');
        $this->renderFile($tool, $source, $tmpTarget);

        $this->fileUtils->copyFile(
            new PhingFile($tmpTarget),
            new PhingFile($target),
            true, false, $this->filterChains,
            $this->getProject(), $this->mode
        );
        unlink($tmpTarget);
    }



    /**
     * Renders a single file with the rST tool.
     *
     * @param string $tool   conversion tool to use
     * @param string $source rST source file
     * @param string $target target file name
     *
     * @return void
     *
     * @throws BuildException When the conversion fails
     */
    protected function renderFile($tool, $source, $target)
    {
        $cmd = $tool
            . ' --exit-status=2'
            . ' ' . escapeshellarg($source)
            . ' ' . escapeshellarg($target)
            . ' 2>&1';

        $this->log('command: ' . $cmd, Project::MSG_VERBOSE);
        exec($cmd, $arOutput, $retval);
        if ($retval != 0) {
            $this->log(implode("\n", $arOutput), Project::MSG_INFO);
            throw new BuildException('Rendering rST failed');
        }
        $this->log(implode("\n", $arOutput), Project::MSG_DEBUG);
    }



    /**
     * Finds the rst2* binary path
     *
     * @return string Full path to rst2$format
     *
     * @throws BuildException When the tool cannot be found
     */
    protected function getToolPath()
    {
        $tool = 'rst2' . $this->format;
        $path = System::which($tool);
        if (!$path) {
            throw new BuildException(
                sprintf('"%s" not found. Install python-docutils.', $tool)
            );
        }

        return $path;
    }



    /**
     * Determines and returns the target file name from the
     * input file and the configured target file name.
     *
     * @param string $file   Input file
     * @param string $target Target file name, may be null
     *
     * @return string Target file name
     *
     * @uses $format
     * @uses $targetExt
     */
    public function getTargetFile($file, $target = null)
    {
        if ($target != '') {
            return $target;
        }

        if (strtolower(substr($file, -4)) == '.rst') {
            return substr($file, 0, -3) . self::$targetExt[$this->format];
        }

        return $file . '.'  . self::$targetExt[$this->format];
    }



    /**
     * Nested creator, creates a FileSet for this task
     *
     * @return object The created fileset object
     */
    public function createFileSet()
    {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num-1];
    }



    /**
     * Nested creator, creates one Mapper for this task
     *
     * @return Mapper The created Mapper type object
     *
     * @throws BuildException
     */
    public function createMapper()
    {
        if ($this->mapperElement !== null) {
            throw new BuildException(
                'Cannot define more than one mapper', $this->location
            );
        }
        $this->mapperElement = new Mapper($this->project);
        return $this->mapperElement;
    }



    /**
     * Creates a filterchain, stores and returns it
     *
     * @return FilterChain The created filterchain object
     */
    public function createFilterChain()
    {
        $num = array_push($this->filterChains, new FilterChain($this->project));
        return $this->filterChains[$num-1];
    }
}
?>
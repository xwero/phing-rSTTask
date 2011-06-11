<?php
require_once "phing/Task.php";

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



    /**
     * The setter for the attribute "file"
     */
    public function setFile($file)
    {
        $this->file = $file;
    }



    /**
     * The setter for the attribute "format"
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
     */
    public function setTarget($targetFile)
    {
        $this->targetFile = $targetFile;
    }



    /**
     * The main entry point method.
     */
    public function main()
    {
        $file   = $this->file;
        $target = $this->getTargetFile($file, $this->targetFile);

        $cmd = 'rst2' . $this->format
            . ' --exit-status=2'
            . ' ' . escapeshellarg($file)
            . ' ' . escapeshellarg($target);

        $this->log('command: ' . $cmd, Project::MSG_VERBOSE);
        passthru($cmd, $retval);
        if ($retval != 0) {
            throw new BuildException('Rendering rST failed');
        }
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
    public function getTargetFile($file, $target)
    {
        if ($target != '') {
            return $target;
        }

        if (strtolower(substr($file, -4)) == '.rst') {
            return substr($file, 0, -3) . self::$targetExt[$this->format];
        }

        return $file . '.'  . self::$targetExt[$this->format];
    }
}
?>
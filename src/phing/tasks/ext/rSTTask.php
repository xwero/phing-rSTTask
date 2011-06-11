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
     */
    protected static $supportedFormats = array(
        'html', 'latex', 'man', 'odt', 's5', 'xml'
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
     * The init method: Do init steps.
     */
    public function init() {
        // nothing to do here
    }

    /**
     * The main entry point method.
     */
    public function main()
    {
        echo $this->file;
        echo $this->targetFile;
        echo $this->format;
    }
}
?>
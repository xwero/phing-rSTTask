<?php
require_once "phing/Task.php";

class rSTtask extends Task
{
    /**
     * @var string Taskname for logger
     */
    protected $taskName = 'rST';

    /**
     * Result format, defaults to "html".
     * Possible options: html, latex, man, odt, s5, xml
     *
     * @var string
     */
    protected $format = 'html';

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
     * @var string
     */
    protected $target = null;


    /**
     * The setter for the attribute "file"
     */
    public function setFile($file)
    {
        $this->file = $file;
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
    public function main() {
        print($this->file, $this->target, $this->format);
    }
}
?>
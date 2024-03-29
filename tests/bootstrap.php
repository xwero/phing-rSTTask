<?php
defined('PHING_TEST_BASE') || define('PHING_TEST_BASE', dirname(__FILE__));
set_include_path(
    realpath(dirname(__FILE__) . '/../../phing-trunk/classes/')
    . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../phing-trunk/test/classes/')
    . PATH_SEPARATOR . get_include_path()
    // trunk version of phing classes should take precedence
);

require_once 'phing/Phing.php';
Phing::startup();

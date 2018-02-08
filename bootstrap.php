<?php
//-------
// Please specify your composer dir
//-------
$composer_dir = dirname(__FILE__); // Or your composer default dir



// Include the composer autoloader
if (!file_exists($composer_dir . "/vendor/autoload.php")) {
    exit("We do not found composer auto-loader.");
}
require_once($composer_dir . "/vendor/autoload.php");
#########################################################


## Include config.php from doc-root
include_once('config.php');


<?php
//-------
// Please specify your composer dir
//-------
$composer_dir = $_SERVER['DOCUMENT_ROOT']; // Or your composer default dir



// Include the composer autoloader
if (!file_exists($composer_dir . "/vendor/autoload.php")) {
    exit("We do not found composer auto-loader.");
}
require_once($composer_dir . "/vendor/autoload.php");
#########################################################


## Include config.php from doc-root
include_once('config.php');


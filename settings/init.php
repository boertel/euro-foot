<?php

set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT']);

if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
    require 'settings/dev.php';
    require 'settings/dev.private.php';
} else {
    require 'settings/prod.php';
    require 'settings/prod.private.php';
}

require 'utils.php';

/* Autoload function to load automatically the php class when it's necessary
 * All classes should be in /classes/ and named classename.class.php
 */
function __autoload($className) {
    $classeFile = dirname(__FILE__) . '/../classes/' . strtolower($className) . '.class.php';
    if (file_exists($classeFile)) {
	include_once $classeFile;
    }
}
?>

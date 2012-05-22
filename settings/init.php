<?php

if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
    require 'dev.php';
    require 'dev.private.php';
} else {
    require 'prod.php';
    require 'prod.private.php';
}

?>

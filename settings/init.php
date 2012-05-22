<?php

set_include_path( get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] );

if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
    require 'settings/dev.php';
    require 'settings/dev.private.php';
} else {
    require 'settings/prod.php';
    require 'settings/prod.private.php';
}

require 'classes/db.class.php';

require 'classes/user.class.php';
require 'classes/team.class.php';
require 'classes/game.class.php';
require 'classes/bet.class.php';


?>

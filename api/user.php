<?php

require '../settings/init.php';

if (isset($_GET) && $_GET['access_token']) {
    if (isset($_POST)) {
        if ($user = User::findUsername($_POST['username'])) {
            echo $user;
        } else {
            $user = new User($_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_GET['access_token']);
            try {
                User::add($user);
                echo $user;
            } catch (PDOException $e) {
            }
        }

    } else {
        print_r($_GET);
    }
}

?>

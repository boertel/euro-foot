<?php

require '../settings/init.php';

header('Content-type: application/json');

if (isset($_GET) && $_GET['access_token']) {
    if (isset($_POST)) {
        $user = User::findUsername($_POST['username']);
        if (count($user) == 0) {
            $user = new User($_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_GET['access_token'], 0);
            try {
                User::add($user);
            } catch (PDOException $e) {
            }
        } else {
            $user = $user[0];
        }
        echo $user->toJSON();
    } else {
        print_r($_GET);
    }
}

?>

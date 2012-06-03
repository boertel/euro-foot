<?php

set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT']);

if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
    require 'settings/dev.php';
    require 'settings/dev.private.php';
} else {
    require 'settings/prod.php';
    require 'settings/prod.private.php';
}

require 'facebook-sdk/facebook.php';

$app_id = $FACEBOOK_APP['id'];
$app_secret = $FACEBOOK_APP['secret'];
$app_url = $FACEBOOK_APP['url'];
$scope = $FACEBOOK_APP['scope'];

/*Define the local time in french*/
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

/* Autoload function to load automatically the php class when it's necessary
 * All classes should be in /classes/ and named classename.class.php
 */
function __autoload($className) {
    $classeFile = dirname(__FILE__) . '/../classes/' . strtolower($className) . '.class.php';
    if (file_exists($classeFile)) {
        include_once $classeFile;
    }
}

// Initialise the session 
Session::getInstance();

// Init the Facebook SDK
$facebook = new Facebook(array(
            'appId' => $app_id,
            'secret' => $app_secret,
        ));

// Get the current user
$facebookUser = $facebook->getUser();

// If the user has not installed the app, redirect them to the Auth Dialog
if (!$facebookUser) {
    $loginUrl = $facebook->getLoginUrl(array(
        'scope' => $scope,
        'redirect_uri' => $app_url,
            ));

    print('<script> top.location.href=\'' . $loginUrl . '\'</script>');
    exit();
}

// set a user session if not
if (!Session::getInstance()->isUserConnected()) {

    $userProfile = $facebook->api("/me");
    $username = $userProfile["username"];

    // try to find if user exist in database
    $user = User::findUsername($username);

    // create a user in database if not
    if ($user == null) {
        $last_name = $userProfile["last_name"];
        $first_name = $userProfile["first_name"];
        $email = $userProfile["email"];
        $token = $facebook->getAccessToken();
        $score = 0;

        $newUser = new User($username, $first_name, $last_name, $email, $token, $score);
        User::add($newUser);

        Session::getInstance()->setUserSession($newUser);
    } else {
        Session::getInstance()->setUserSession($user[0]);
    }
}

// From here the user is created in database, and the session is set with the server


// POINTS
$POINTS['perfect'] = 50;
$POINTS['win'] = 20;
$POINTS['lost'] = 0;
?>

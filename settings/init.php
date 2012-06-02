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
$code = $_REQUEST["code"];

// Helper function to get an APP ACCESS TOKEN
function get_app_access_token($app_id, $app_secret) {
    $token_url = 'https://graph.facebook.com/oauth/access_token?'
     . 'client_id=' . $app_id
     . '&client_secret=' . $app_secret
     . '&grant_type=client_credentials';

    $token_response =file_get_contents($token_url);
    $params = null;
    parse_str($token_response, $params);
    return  $params['access_token'];
}

function fb_permissions() {
    global $app_id, $app_url, $scope;
    $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
    . $app_id . "&redirect_uri=" . urlencode($app_url) . "&scope=" . $scope;

    echo("<script> top.location.href='" . $dialog_url . "'</script>");
}

if(empty($code)) {
    fb_permissions();
} else {
    $token_url = "https://graph.facebook.com/oauth/access_token?"
      . "client_id=" . $app_id . "&redirect_uri=" . urlencode($app_url)
      . "&client_secret=" . $app_secret . "&code=" . $code;

    $response = file_get_contents($token_url);
    if (empty($response)) {
        fb_permissions();
    } else {

        $params = null;
        parse_str($response, $params);

        $graph_url = "https://graph.facebook.com/me?access_token=" 
          . $params['access_token'];

        $fb_user = json_decode(file_get_contents($graph_url));

         // try to find if user exist in database
        $username = $fb_user->username;
        $user = User::findUsername($username);

        // create a user in database if not
        if ($user == null) {
            $last_name = $fb_user->last_name;
            $first_name = $fb_user->first_name;
            $email = $fb_user->email;
            $token = $params['access_token'];
            $score = 0;

            $newUser = new User($username, $first_name, $last_name, $email, $token, $score);
            User::add($newUser);

            Session::getInstance()->setUserSession($newUser);
        } else {
            $user[0]->setToken($params['access_token']);
            Session::getInstance()->setUserSession($user[0]);
        }
    }
}

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


$app_access_token = get_app_access_token($app_id, $app_secret);
$facebook->setAccessToken($app_access_token);

// Handle the facebook request (like someone accepting the invite of a friend)
if (isset($_REQUEST['request_ids'])) {
    $requestIDs = explode(',', $_REQUEST['request_ids']);
    foreach ($requestIDs as $requestID) {
        try {
            $delete_success = $facebook->api('/' . $requestID, 'DELETE');
        } catch (FacebookAPIException $e) {
            // ignore error
            // error_log($e);
        }
    }
}

// POINTS
$POINTS['perfect'] = 50;
$POINTS['win'] = 20;
$POINTS['lost'] = 0;
?>

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
    redirectUserToLogin($facebook,$scope,$app_url);
}

$facebookProfil = $facebook->api("/me");
 
// set a user session if not
if (!Session::getInstance()->isUserConnected()) {
    connectUser($facebook,$facebookProfil);
}else{
    // if the user is already connected, check that the current user session correspond to the current facebook user
    $user = Session::getInstance()->getUserSession();
    if($user->getUsername() != $facebookProfil["username"]){
        Session::getInstance()->disconnectUser();
        connectUser($facebook,$facebookProfil);
    }
}

//
// From here the user is created in database, and the session is set with the server
//

// update acces token if necessary
$user = Session::getInstance()->getUserSession();
if($facebook->getAccessToken() != $user->getToken()){
    $user->setToken($facebook->getAccessToken());
    User::update($user);
    Session::getInstance()->setUserSession($user);
}

// POINTS
$POINTS['perfect'] = 50;
$POINTS['win'] = 20;
$POINTS['lost'] = 0;


/**
 * Redirect the user to the login facebook page. Stop php script.
 * 
 * @param type $facebook the instance of the facebook SDK
 * @param type $scope permissions asked to the user
 * @param type $app_url $the redirect url after the user accepted permissions
 */
function redirectUserToLogin($facebook,$scope,$app_url){
    $loginUrl = $facebook->getLoginUrl(array(
        'scope' => $scope,
        'redirect_uri' => $app_url,
            ));

    print('<script> top.location.href=\'' . $loginUrl . '\'</script>');
    exit();
}

/**
 * Connect the user (set session)
 * @param type $facebook the instance of facebook SDK class
 * @param type $facebookProfil user data comming from facebook
 */
function connectUser($facebook, $facebookProfil){
    $facebookId= $facebookProfil['id'];
    
    // try to find if user exist in database
    $user = User::findUserByFacebookId($facebookId);

    // create a user in database if not
    if ($user == null) {
        $username = $facebookProfil["username"];
        $last_name = $facebookProfil["last_name"];
        $first_name = $facebookProfil["first_name"];
        $email = $facebookProfil["email"];
        $token = $facebook->getAccessToken();
        $score = 0;

        $newUser = new User($facebookId, $username, $first_name, $last_name, $email, $token, $score);
        User::add($newUser);

        Session::getInstance()->setUserSession($newUser);
    } else {
        Session::getInstance()->setUserSession($user[0]);
    }
}

/**
 * This method make an https call so don't call it multiple times in a same script
 * 
 * @param type $app_id
 * @param type $app_secret
 * @return app access token (not user acess token) 
 */
function getAppAccesToken($app_id, $app_secret){
        $token_url =    "https://graph.facebook.com/oauth/access_token?" . 
            "client_id=" . $app_id . 
            "&client_secret=" . $app_secret . 
            "&grant_type=client_credentials"; 
        $reponse = file_get_contents($token_url);
        
        $param = null;
        parse_str($reponse,$param);
        
        return $param['access_token'];
    }
?>

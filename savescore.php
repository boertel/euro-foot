<?php
require 'settings/init.php';
require 'facebook-sdk/facebook.php';


$facebook = new Facebook(array(
    'appId'  => $FACEBOOK_APP['id'],
    'secret' => $FACEBOOK_APP['secret'],
));

$user = $facebook->getUser();

$app_access_token = get_app_access_token($app_id, $app_secret);
$facebook->setAccessToken($app_access_token);
$response = $facebook->api('/' . $user . '/scores', 'post', array(
    'score' => $score,
));
print($response);

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

?>

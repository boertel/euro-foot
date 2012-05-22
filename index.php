<?php

require 'settings/init.php';
require 'facebook-sdk/facebook.php';

$app_id = $FACEBOOK_APP['id'];
$app_secret = $FACEBOOK_APP['secret'];
$app_namespace = $FACEBOOK_APP['namespace'];
$app_url = $FACEBOOK_APP['namespace'];
$scope = $FACEBOOK_APP['scope'];

echo $ENV;
/*
// Init the Facebook SDK
$facebook = new Facebook(array(
'appId'  => $app_id,
'secret' => $app_secret,
));

// Get the current user
$user = $facebook->getUser();

// If the user has not installed the app, redirect them to the Auth Dialog
if (!$user) {
    $loginUrl = $facebook->getLoginUrl(array(
        'scope' => $scope,
        'redirect_uri' => $app_url,
    ));

    print('<script> top.location.href=\'' . $loginUrl . '\'</script>');
}
*/
?>

<?php

require 'settings/init.php';
require 'facebook-sdk/facebook.php';


$title = "Euro Foot 2012";

$app_id = $FACEBOOK_APP['id'];
$app_secret = $FACEBOOK_APP['secret'];
$app_namespace = $FACEBOOK_APP['namespace'];
$app_url = 'https://apps.facebook.com/' . $app_namespace . '/';
$scope = $FACEBOOK_APP['scope'];

// Init the Facebook SDK
$facebook = new Facebook(array(
 'appId'  => $app_id,
 'secret' => $app_secret,
));

$fb_user = $facebook->getUser();

if ($fb_user) {
    $user_profile = $facebook->api("/me");
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
</head>
<body>



</body>
</html>

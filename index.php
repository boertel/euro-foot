<?php
require 'settings/init.php';
require 'facebook-sdk/facebook.php';


$title = "Euro Foot 2012";

$app_id = $FACEBOOK_APP['id'];
$app_secret = $FACEBOOK_APP['secret'];
$app_url = $FACEBOOK_APP['url'];
$scope = $FACEBOOK_APP['scope'];
?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="includes/js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="includes/js/jquery-ui-1.8.20.custom.min.js"></script>
        <link type="text/css" href="includes/css/custom-theme/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
        <link type="text/css" href="template/style.css" rel="stylesheet" />
        <style type="text/css">
            /*demo page css*/
            ul#icons {margin: 0; padding: 0;}
            ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
            ul#icons span.ui-icon {float: left; margin: 0 4px;}
        </style>
    </head>
    <body>

        <?php
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
        } else {

            $userProfile = $facebook->api("/me");
            $username = $userProfile["username"];

            // set a user session if not
            if (!Session::getInstance()->isUserConnected()) {
                // try to find if user exist in database
                $user = User::findUsername($username);

                // create a user in database if not
                if ($user == null) {
                    $last_name = $userProfile["name"];
                    $first_name = $userProfile["first_name"];
                    $email = $userProfile["email"];
                    $token = "???";
                    $score = 0;

                    $newUser = new User($username, $first_name, $last_name, $email, $token, $score);
                    User::add($newUser);

                    Session::getInstance()->setUserSession($newUser);
                } else {
                    Session::getInstance()->setUserSession($user[0]);
                }
            }

            // From here the user is created in database, and the session is set with the server
            require "template/bets.php";
        }
        ?>
    </body>
</html>

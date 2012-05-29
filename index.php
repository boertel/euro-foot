<?php
require 'settings/init.php';
require 'facebook-sdk/facebook.php';

$title = 'Euro 2012 - À vos paris';
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
        <div id="fb-root"></div>
        <script src="http://connect.facebook.net/en_US/all.js"></script>
        <script type="text/javascript">
            FB.init({
                appId  : '<?php echo $app_id; ?>',
                frictionlessRequests: false
            });

            function sendRequestViaMultiFriendSelector() {
                FB.ui({method: 'apprequests',
                    title: '<?php echo $title;?>',
                    message: 'Parie sur les match de l\'Euro 2012 et deviens le meilleur de tes amis !',
                }, requestCallback);
            }
      
            function requestCallback(response) {
                // Handle callback here
            }
        </script>

        <?php
        // Init the Facebook SDK
        $facebook = new Facebook(array(
                    'appId' => $app_id,
                    'secret' => $app_secret,
                ));

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

            // Check if the user wins his bets.
        }
        ?>
    </body>
</html>

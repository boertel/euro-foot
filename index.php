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
        }
        
        $userProfile = $facebook->api("/me");
        $username = $userProfile["username"];
        
        // set a user session if not
        if(!Session::getInstance()->isUserConnected()){
            // try to find if user exist in database
            $user = User::findUsername($username);
            
            // create a user in database if not
            if($user == null){
                $last_name = $userProfile["name"];
                $first_name = $userProfile["first_name"];
                $email = $userProfile["email"];
                $token =  "???";
                $score = 0;
                
                $newUser = new User($username, $first_name, $last_name, $email, $token, $score);
                User::add($newUser);
                
                Session::getInstance()->setUserSession($newUser);
            }else{
                Session::getInstance()->setUserSession($user[0]);
            }
        }
        
        // From here the user is created in database, and the session is set with the server
        var_dump(Session::getInstance()->getUserSession());
   
        // Include IHM Here !
        ?>
    </body>
</html>

<?php require 'settings/init.php';

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

$title = 'Euro 2012 - À vos paris';?>

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
    </head>
    <body>
        <div id="fb-root"></div>
        <?php
        // INCLUDE score update here, before header
        ?>
        <div id="header">
            <div id="userProfilBackground"></div>
            <div id="userProfilData">
                <?php
                if (Session::getInstance()->isUserConnected()) {
                    $user = Session::getInstance()->getUserSession();
                    echo '<p class="center bold title">' . $user->getFirst_name() . ' ' . $user->getLast_name() . '</p>';
                    echo '<p class="score">Score : <span class="bold">' . $user->getScore() . '</span></p>';
                }
                ?>
                <p>
                    <a href="#" class="FBButton" onclick="sendRequestViaMultiFriendSelector(); return false;">Invitez vos amis</a>
                </p>
            </div>
        </div>
        <script src="http://connect.facebook.net/en_US/all.js"></script>
        <script type="text/javascript">
            FB.init({
                appId  : '<?php echo $app_id; ?>',
                frictionlessRequests: false
            });

            function sendRequestViaMultiFriendSelector() {
                FB.ui({method: 'apprequests',
                    title: '<?php echo $title; ?>',
                    message: 'Parie sur les match de l\'Euro 2012 et deviens le meilleur de tes amis !',
                    display: 'iframe',
                    <?php
                        echo " access_token:'".Session::getInstance()->getUserSession()->getToken()."'";
                    ?>
                }, requestCallback);
            }
      
            function requestCallback(response) {
                // Handle callback here
            }
        </script>
        <?php
        // Include the tab to bet
        require "template/bets.php";
        ?>
        <div id="footer"></div>
        <p class="center">Design by <a href="http://www.dinozef-design.fr" title="Dinozef Design, créations graphiques" target="_blank">Simon</a></p>
    </body>
</html>

<?php

require 'settings/init.php';
require 'facebook-sdk/facebook.php';

global $FACEBOOK_APP;

$app_id = $FACEBOOK_APP['id'];
$app_namespace = $FACEBOOK_APP['namespace'];
$app_url = $FACEBOOK_APP['url'];
$scope = $FACEBOOK_APP['scope'];

$title = "Euro Foot 2012";

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
</head>
<body>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>

<?php

require 'templates/connect.php';

?>

<script type="text/javascript" src="static/js/ef.js"></script>

<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : <?php echo $FACEBOOK_APP['id']; ?>, // App ID
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true  // parse XFBML
        });

        // Additional initialization code here
        EF.getLoginStatus();
    };

    // Load the SDK Asynchronously
    (function(d){
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        ref.parentNode.insertBefore(js, ref);
    }(document));
</script>


</body>
</html>

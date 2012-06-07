<?php
require 'settings/init.php';

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

$title = 'Euro 2012 - À vos paris';
?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="includes/js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="includes/js/jquery-ui-1.8.20.custom.min.js"></script>
        <script type="text/javascript" src="//connect.facebook.net/en_US/all.js"></script>

        <link type="text/css" href="includes/css/custom-theme/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
        <link type="text/css" href="template/style.css" rel="stylesheet" />
        <script type="text/javascript">
            $(function() {
                $( "#rules" ).dialog({
                    autoOpen: false,
                    title: 'Règles',
                    resizable: false,
                    draggable: false,
                    position: ['center',200],
                    width: '70%',
                    minWidth: 760,
                    maxWidth: 900,
                    modal: true
                });

                $( ".openRules" ).click(function() {
                    $( "#rules" ).dialog( "open" );
                    return false;
                });
            });
        
            FB.init({
                appId  : '<?php echo $app_id; ?>',
                frictionlessRequests: false
            });

            function sendRequestViaMultiFriendSelector() {
                FB.ui({method: 'apprequests',
                    title: '<?php echo $title; ?>',
                    message: 'Parie sur les match de l\'Euro 2012 et deviens le meilleur de tes amis !',
                    display: 'iframe',
                    access_token: '<?php echo Session::getInstance()->getUserSession()->getToken(); ?>'
                }, requestCallback);
            }
      
            function requestCallback(response) {
                // Handle callback here
            }
        </script>
    </head>
    <body>
        <div id="fb-root"></div>
        <?php
        // update score
        $user = Session::getInstance()->getUserSession();
        $result = Db::request("SELECT b.id as bet_id, g.score_a as game_score_a, g.score_b as game_score_b, b.score_a as bet_score_a, b.score_b as bet_score_b FROM bet b JOIN game g ON g.id = b.game_id WHERE g.score_a is not NULL AND g.score_b is not NULL AND b.user_id = " . $user->getId() . " AND b.validated = false");
        $bets = $result->fetchAll(PDO::FETCH_ASSOC);
        $points = 0;

        foreach ($bets as $bet) {
            $betScoreTeamA = $bet['bet_score_a'];
            $betScoreTeamB = $bet['bet_score_b'];
            $scoreTeamA = $bet['game_score_a'];
            $scoreTeamB = $bet['game_score_b'];

            if ($betScoreTeamA == null && $betScoreTeamB == null) {
                $points += $POINTS['lost'];
            } else if ($scoreTeamA == $betScoreTeamA && $scoreTeamB == $betScoreTeamB) {
                $points += $POINTS['perfect'];
            } else if (($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB)
                    || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB)
                    || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)) {
                $points += $POINTS['win'];
            } else {
                $points += $POINTS['lost'];
            }
            Db::request("UPDATE bet SET validated = true WHERE id = " . $bet['bet_id'] . "");
        }

        if ($points > 0) {
            try {

                $access_token = $facebook->getAppId() . '|' . $facebook->getAppSecret();  // found this on Internet. Not sure that's quite secure to give app secret... Use method below if not
                //$access_token = getAppAccesToken($app_id, $app_secret); maybe use this instead ? But php_openssl module have to be set
                $facebook->api('/' . $facebook->getUser() . '/scores', 'post', array('score' => $user->getScore() + $points, 'access_token' => $access_token));

                $user->setScore($user->getScore() + $points);
                User::update($user);
                Session::getInstance()->setUserSession($user);
            } catch (Exception $e) {
                
            }
        }
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
                    <a href="rules.php" class="FBButton openRules">Règles</a>
                </p>
            </div>
        </div>

        <?php require "template/bets.php"; ?>

        <div id="footer"></div>
        <p class="center">Design by <a href="http://www.dinozef-design.fr" title="Dinozef Design, créations graphiques" target="_blank">Simon</a></p>
        <div id="rules">
            <p>Pariez sur les matchs de l'Euro 2012 pour gagner des points et devenir le meilleur de vos amis, voire de Facebook ! 
                Vous avez jusqu'au début du match pour parier. C'est le score à la fin du temps réglementaire qui est pris en compte pour ne pas 
                fausser les statistiques lors des phases finales. Vos points sont mis à jour à chaque fois que vous vous connectez sur l'application
                et que de nouveaux résultats sont disponibles.
            </p> 
            <br />
            <ul>
                <li>Un pari parfait rapporte <span class="bold"><?php echo $POINTS['perfect']; ?></span> points. Exemple : Résultat : 3 - 2 et Pari : 3 - 2</li>
                <li>Un bon pari rapporte <span class="bold"><?php echo $POINTS['win']; ?></span> points. Exemples : Résultat : 2 - 3 et Pari : 2 - 4, ou Résultat : 2 - 2, et Pari : 1 - 1</li>
                <li>Un mauvais pari ne vous rapporte <span class="bold">aucun</span> point. Exemples : Résultat : 2 - 3 et Pari : 0 - 0, ou Résultat : 3 - 1 et Pari : 2 - 4</li>
            </ul>
            <br />
            <p>Vous pourrez distinguer les matchs que vous avez gagnés ou perdus de cette façon :</p>
            <div class="match">
                <span class="matchDate">lun. 11/06 18:00</span>
                <span class="matchTeamA">FRANCE <img src="includes/pictures/flags/fr.png" alt="FR" /></span>
                <span class="matchScoreEnd win">Pari parfait (10 - 0 :)) <span class="points"><span class="perfect">+ <?php echo $POINTS['perfect']; ?></span></span></span>
                <span class="matchTeamB"><img src="includes/pictures/flags/england.png" alt="EN"/> ANGLETERRE</span>
            </div>
            <div class="match">
                <span class="matchDate">dim. 10/06 18:00</span>
                <span class="matchTeamA">ESPAGNE <img src="includes/pictures/flags/es.png" alt ="ES" /></span>
                <span class="matchScoreEnd win">Bon pari <span class="points">+ <?php echo $POINTS['win']; ?></span></span>
                <span class="matchTeamB"><img src="includes/pictures/flags/it.png" alt="IT" /> ITALIE</span>
            </div>
            <div class="match">
                <span class="matchDate">sam. 09/06 20:45</span>
                <span class="matchTeamA">ALLEMAGNE <img src="includes/pictures/flags/de.png" alt="DE"/></span>
                <span class="matchScoreEnd loose">Mauvais pari <span class="points">= 0</span></span>
                <span class="matchTeamB"><img src="includes/pictures/flags/pt.png" alt="PT" /> PORTUGAL</span>
            </div>    
        </div>
    </body>
</html>

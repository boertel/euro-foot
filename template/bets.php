<script type="text/javascript">
    $(function(){
        // Tabs
        $('#groupes').tabs();
    });

    function modifyBet(idMatch){
        $(document).ready(function() {
            var idMatchScoreInput = '#matchScoreInput_'+idMatch;
            var idMatchScore = '#matchScore_'+idMatch;
		
            $(idMatchScoreInput).css("display","inline-block");
            $(idMatchScore).hide();
        });
    }

    function saveBet(gameId){
        $(document).ready(function() {
            var idMatchScoreInput = '#matchScoreInput_'+gameId;
            var idMatchScore = '#matchScore_'+gameId;

            var scoreA = $('#scoreA_match_'+gameId).val();
            var scoreB = $('#scoreB_match_'+gameId).val();

            $.post("saveBet.php", { gameId: gameId, scoreA: scoreA, scoreB: scoreB});
		
            $(idMatchScoreInput).hide();

            $(idMatchScore).html('Pari : '+scoreA +' - '+scoreB);
            $(idMatchScore).css("display","inline-block");
        });
    }
</script>

<?php
    // move all this part before the header picture in index.php because the header contains the score

    $user = Session::getInstance()->getUserSession();
    $result = Db::request("SELECT b.id as bet_id, g.score_a as game_score_a, g.score_b as game_score_b, b.score_a as bet_score_a, b.score_b as bet_score_b FROM bet b JOIN Game g ON g.id = b.game_id WHERE g.score_a is not NULL AND g.score_b is not NULL AND b.user_id = " . $user->getId() . " AND b.validated = false");
    $bets = $result->fetchAll(PDO::FETCH_ASSOC);
    $points = 0;
    
    foreach ($bets as $bet) {
        $betScoreTeamA = $bet['bet_score_a'];
        $betScoreTeamB = $bet['bet_score_b'];
        $scoreTeamA = $bet['game_score_a'];
        $scoreTeamB = $bet['game_score_b'];

        if($betScoreTeamA == null && $betScoreTeamB == null){
            $points += $POINTS['lost']; 
        }
        else if($scoreTeamA == $betScoreTeamA && $scoreTeamB == $betScoreTeamB) {
            $points += $POINTS['perfect'];
        } else if (($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB) 
                    || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB) 
                    || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)){
            $points += $POINTS['win'];
        } else {
            $points += $POINTS['lost'];
        }
        //Db::request("UPDATE bet SET validated = true WHERE id = " . $bet['bet_id'] . "");    
    }
    
    if($points > 0){ 
        $user->setScore($user->getScore()+$points);
        
        $access_token = $facebook->getAppId().'|'.$facebook->getAppSecret();  // found this on Internet. Not sure that's quite secure to give app secret... Use method below if not
        //$access_token = getAppAccesToken($app_id, $app_secret); maybe use this instead ? But php_openssl module have to be set
        $facebook->api('/' . $facebook->getUser() . '/scores', 'post', array('score' => $user->getScore(), 'access_token' => $access_token));
        
        User::update($user);
        Session::getInstance()->setUserSession($user); 
    }
?>
<!-- Tabs -->
<div id="groupes">
    <ul>
        <?php
        $groups = Group::findAll();
        $games = Game::findAll();
        $teams = Team::findAll();
        $bets = Bet::findAllBetsForUser(Session::getInstance()->getUserSession());
        $currentUTCTimestamp = gmmktime();
            
        for($i = 0; $i < sizeof($groups);$i++){
            echo '<li><a href="#groupe'.$groups[$i]->getId().'">'.$groups[$i]->getTitle().'</a></li>';
        }
        ?>
        <li><a href="#leaderbord">Classement</a></li>
    </ul>
        <?php
        foreach($groups as $group){
            $groupId = $group->getId();
            $gamesForThisGroup = getGamesForGroup($games, $groupId);

            echo '<div id="groupe'.$groupId.'">';
                foreach($gamesForThisGroup as $game){
                    $teamA = getTeam($teams, $game->getTeam_a());
                    $teamB = getTeam($teams, $game->getTeam_b());
                    $bet = getBet($bets, $game->getId());
                    $gameUTCTimestamp = strtotime($game->getStart_date()); // to display add 2h (7200sec) because FRANCE is GMT+2
                    
                    echo '<div class="match">
                            <span class="matchDate">'.strftime("%a %d/%m %H:%M",$gameUTCTimestamp+7200).'</span>
                            <span class="matchTeamA">'.$teamA->getName().' <img src="includes/pictures/flags/'.$teamA->getFlag().'"></span>';
                            if($currentUTCTimestamp < $gameUTCTimestamp){
                                displayBetFormular($game->getId(),$bet->getScore_a(),$bet->getScore_b());
                            } else {
                                displayBetResult($POINTS, $game->getScore_a(),$game->getScore_b(),$bet->getScore_a(),$bet->getScore_b());
                            }
                            echo '<span class="matchTeamB"><img src="includes/pictures/flags/'.$teamB->getFlag().'"> '.$teamB->getName().'</span>'
                    .'</div>';
                }
            echo '</div>';
        }?>
    
        <div id="leaderbord">
            <div class="scores ui-widget ui-widget-content ui-corner-all">
                <div class="widget-title ui-state-default ui-corner-all">Amis</div>
                <?php $scores = $facebook->api('/'.$app_id.'/scores');
                for($position = 0; $position < sizeof($scores['data']); $position++){
                    $userInfos = $scores['data'][$position]['user'];
                    $score = $scores['data'][$position]['score']?>
                    <div class="score">
                        <div class="rank"><span class="rank<?php echo $position+1;?>"><?php echo $position+1;?></span></div>
                        <img src="http://graph.facebook.com/<?php echo $userInfos['id'];?>/picture" class="friendPicture"/>
                        <div class="friendInfo">
                            <span class="bold"><?php echo $userInfos['name'];?></span><br />
                            Score : <?php echo $score;?>
                        </div>
                    </div>
                <?php }?>
            </div>
            <div class="scores ui-widget ui-widget-content ui-corner-all">
                <div class="widget-title ui-state-default ui-corner-all ui-helper-clearfix">Général</div>
                
                <?php $users = User::findAllOrderByScore();
                for($position = 0; $position < sizeof($users); $position++){
                    $user = $users[$position];?>
                    <div class="score">
                        <div class="rank"><span class="rank<?php echo $position+1;?>"><?php echo $position+1;?></span></div>
                        <img src="http://graph.facebook.com/<?php echo $user->getFacebookId();?>/picture" class="friendPicture"/>
                        <div class="friendInfo">
                            <span class="bold"><?php echo $user->getFirst_name().' '.$user->getLast_name();?></span><br />
                            Score : <?php echo $user->getScore();?>
                        </div>
                    </div>
                <?php }?>
            </div>
            <div class="clear"></div>
        </div>
    
        <?php function getGamesForGroup($games, $groupId){
            $gamesForThisGroup = array();
            foreach($games as $game){
                if($game->getId_group() == $groupId){
                    array_push($gamesForThisGroup, $game);
                }
            }

            return $gamesForThisGroup;
        }
        
        function getTeam($teams, $teamId){
            foreach($teams as $team){
                if($team->getId() == $teamId){
                    return $team;
                }
            }
        }
        
        function getBet($bets, $gameId){
            if($bets != null){
                foreach($bets as $bet){
                    if($bet->getMatch_id() == $gameId){
                        return $bet;
                    }
                }
            }
            return new Bet(null,null,null,null,null); // return an empty bet if none is found (i.e. user didn't bet on this match)
        }
        
        function displayBetFormular($gameId, $betScoreTeamA, $betScoreTeamB){
            echo '<span id="matchScore_'.$gameId.'" class="matchScore" title="Modifier le paris" onclick="modifyBet('.$gameId.')">Pari : '.$betScoreTeamA.' - '.$betScoreTeamB.'</span>
                  <span id="matchScoreInput_'.$gameId.'" class="matchScoreInput">
                    <select id="scoreA_match_'.$gameId.'" name="scoreA_match_'.$gameId.'">';
                        displayPossibleScores($betScoreTeamA);
                    echo '</select>
                        - 
                    <select id="scoreB_match_'.$gameId.'" name="scoreB_match_'.$gameId.'">';
                        displayPossibleScores($betScoreTeamB);
                    echo '</select>
                    <button id="saveButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                            role="button" aria-disabled="false" onclick="saveBet('.$gameId.')">
                        <span class="ui-button-text">OK</span>
                    </button>
            </span>';
        }
        
        function displayPossibleScores($defaultValue){
            for($score = 0; $score <= 20 ; $score ++){
                if($score == $defaultValue){
                    echo '<option value="'.$score.'" selected="selected">'.$score.'</option>';
                }else{
                    echo '<option value="'.$score.'">'.$score.'</option>';
                } 
            }
        }
        
        function displayBetResult($POINTS, $scoreTeamA, $scoreTeamB, $betScoreTeamA, $betScoreTeamB){
            if($scoreTeamA == null && $scoreTeamB == null){
                $betResult = 'En cours';
                $betResultClass = '';
                $perfect = '';
                $point = '';
            }else{
                $betResult = 'Résultat : '.$scoreTeamA.' - '.$scoreTeamB;
                if($betScoreTeamA == null && $betScoreTeamB == null){
                    // user didn't bet
                    $betResultClass = ' loose';
                    $perfect = '';
                    $point = '<span class="points">= '.$POINTS['lost'].' </span>';
                }
                else if($scoreTeamA == $betScoreTeamA &&  $scoreTeamB == $betScoreTeamB){
                    // user made a perfect bet
                    $betResultClass = ' win';
                    $point = '<span class="points">+ '.$POINTS['perfect'].' <img src="includes/pictures/exact.png"  width="16" height="16" alt ="exact" title="Bonus pari exact" /></span>';
                }else if(($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB) 
                            || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB) 
                            || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)){
                    // user made a winning bet
                    $betResultClass = ' win';
                    $perfect = '';
                    $point = '<span class="points">+ '.$POINTS['win'].'</span>';
                }else{
                    // user made a loosing bet
                    $betResultClass = ' loose';
                    $perfect = ''; 
                    $point = '<span class="points">= '.$POINTS['lost'].' </span>';
                }
            }
            echo '<span class="matchScoreEnd'.$betResultClass.'">'.$betResult.' // Pari : '.$betScoreTeamA.' - '.$betScoreTeamB.' '.$point.'</span>'; 
        }      
    ?>
</div>

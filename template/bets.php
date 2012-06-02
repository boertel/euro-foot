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
    $user = Session::getInstance()->getUserSession();
    $result = Db::request("SELECT b.id as bet_id, g.score_a as game_score_a, g.score_b as game_score_b, b.score_a as bet_score_a, b.score_b as bet_score_b FROM bet b JOIN Game g ON g.id = b.game_id WHERE g.score_a is not NULL AND g.score_b is not NULL AND b.user_id = " . $user->getId() . " AND b.validated = false");
    $bets = $result->fetchAll(PDO::FETCH_ASSOC);
    foreach ($bets as $bet) {
        $points = 0;
        $betScoreTeamA = $bet['bet_score_a'];
        $betScoreTeamB = $bet['bet_score_b'];
        $scoreTeamA = $bet['game_score_a'];
        $scoreTeamB = $bet['game_score_b'];

        if($betScoreTeamA == null && $betScoreTeamB == null){
            $points = $POINTS['lost']; 
        }
        else if($scoreTeamA == $betScoreTeamA && $scoreTeamB == $betScoreTeamB) {
            $points = $POINTS['perfect'];
        } else if (($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB) 
                    || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB) 
                    || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)){
            $points = $POINTS['win'];
        } else {
            $points = $POINTS['lost'];
        }
        $response = $facebook->api('/' . $facebook->getUser() . '/scores', 'post', array('score' => 20));
        Db::request("UPDATE bet SET validated = true WHERE id = " . $bet['bet_id'] . ""); 
        $user = Session::getInstance()->getUserSession();
        Db::request("UPDATE user SET score = score + " . $points . " WHERE id = " . $user->getId() . "");
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
                                displayBetResult($game->getScore_a(),$game->getScore_b(),$bet->getScore_a(),$bet->getScore_b());
                            }
                            echo '<span class="matchTeamB"><img src="includes/pictures/flags/'.$teamB->getFlag().'"> '.$teamB->getName().'</span>'
                    .'</div>';
                }
            echo '</div>';
        }
            
        function getGamesForGroup($games, $groupId){
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
        
        function displayBetResult($scoreTeamA, $scoreTeamB, $betScoreTeamA, $betScoreTeamB){
            if($scoreTeamA == null && $scoreTeamB == null){
                $betResult = 'En cours';
                $betResultClass = '';
                $perfect = '';
            }else{
                $betResult = 'Résultat : '.$scoreTeamA.' - '.$scoreTeamB;
                if($betScoreTeamA == null && $betScoreTeamB == null){
                    // user didn't bet
                    $betResultClass = ' loose';
                    $perfect = ''; 
                }
                else if($scoreTeamA == $betScoreTeamA &&  $scoreTeamB == $betScoreTeamB){
                    // user made a perfect bet
                    $betResultClass = ' win';
                    $perfect = '<img src="includes/pictures/exact.png"  width="16" height="16" alt ="exact" title="Bonus pari exact" />';
                }else if(($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB) 
                            || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB) 
                            || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)){
                    // user made a winning bet
                    $betResultClass = ' win';
                    $perfect = '';
                    
                }else{
                    // user made a loosing bet
                    $betResultClass = ' loose';
                    $perfect = ''; 
                }
            }
            echo '<span class="matchScoreEnd'.$betResultClass.'">'.$betResult.' // Pari : '.$betScoreTeamA.' - '.$betScoreTeamB.' '.$perfect.'</span>'; 
        }      
    ?>
</div>

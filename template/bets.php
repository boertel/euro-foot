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

<!--<p>
    <a href="#" onclick="sendRequestViaMultiFriendSelector(); return false;">Invitez vos amis</a>
</p>-->

<?php
    $user = Session::getInstance()->getUserSession();
    $result = Db::request("SELECT * FROM bet b JOIN Game g ON g.id = b.game_id WHERE g.score_a != NULL AND g.score_b != NULL AND b.user_id = " . $user->getId() . " AND b.validated = false");
    echo "result:" . $result->fetch(PDO::FETCH_NUM);
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
                    
                    echo '<div class="match">
                            <span class="matchDate">'.$game->getStart_date().'</span>
                            <span class="matchTeamA">'.$teamA->getName().' <img src="includes/pictures/flags/'.$teamA->getFlag().'"></span>';
                            if($currentUTCTimestamp < strtotime($game->getStart_date())){
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
            echo '<span id="matchScore_'.$gameId.'" class="matchScore" title="Modifier le paris" onclick="modifyBet('.$gameId.')">Pari : '.$betScoreTeamB.' - '.$betScoreTeamB.'</span>
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

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
                            <span class="matchTeamA">'.$teamA->getName().' <img src="includes/pictures/flags/'.$teamA->getFlag().'" alt="'.strtoupper($teamA->getFlag()).'" /></span>';
                            if($currentUTCTimestamp < $gameUTCTimestamp){
                                displayBetFormular($game->getId(),$bet->getScore_a(),$bet->getScore_b());
                            } else {
                                displayBetResult($POINTS, $game->getScore_a(),$game->getScore_b(),$bet->getScore_a(),$bet->getScore_b());
                            }
                            echo '<span class="matchTeamB"><img src="includes/pictures/flags/'.$teamB->getFlag().'" alt="'.strtoupper($teamB->getFlag()).'" /> '.$teamB->getName().'</span>'
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
                        <img src="http://graph.facebook.com/<?php echo $userInfos['id'];?>/picture" class="friendPicture" alt="non dispo"/>
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
                        <img src="http://graph.facebook.com/<?php echo $user->getFacebookId();?>/picture" class="friendPicture" alt="non dispo"/>
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
            if($betScoreTeamA == null && $betScoreTeamB == null){
                $bet = 'Pari : ?';
            }else{
                $bet = 'Pari : '.$betScoreTeamA.' - '.$betScoreTeamB;
            }
            
            echo '<span id="matchScore_'.$gameId.'" class="matchScore" title="Modifier le paris" onclick="modifyBet('.$gameId.')">'.$bet.'</span>
                  <span id="matchScoreInput_'.$gameId.'" class="matchScoreInput">
                    <select id="scoreA_match_'.$gameId.'" name="scoreA_match_'.$gameId.'">';
                        displayPossibleScores($betScoreTeamA);
                    echo '</select>
                        - 
                    <select id="scoreB_match_'.$gameId.'" name="scoreB_match_'.$gameId.'">';
                        displayPossibleScores($betScoreTeamB);
                    echo '</select>
                    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                            role="button" aria-disabled="false" onclick="saveBet('.$gameId.')" type="button">
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
                    $point = '<span class="points">= '.$POINTS['lost'].'</span>';
                }
                else if($scoreTeamA == $betScoreTeamA &&  $scoreTeamB == $betScoreTeamB){
                    // user made a perfect bet
                    $betResultClass = ' win';
                    $point = '<span class="points"><span class="perfect">+ '.$POINTS['perfect'].'</span></span>';
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
                    $point = '<span class="points">= '.$POINTS['lost'].'</span>';
                }
            }
            
            if($betScoreTeamA == null && $betScoreTeamB == null){
                $bet = 'Pari : Aucun';
            }else{
                $bet = 'Pari : '.$betScoreTeamA.' - '.$betScoreTeamB;
            }
            
            echo '<span class="matchScoreEnd'.$betResultClass.'">'.$betResult.' // '.$bet.' '.$point.'</span>'; 
        }      
    ?>
</div>

<script type="text/javascript">
            
    $(function() {
        $( ".showFriendsBetDialog" ).dialog({
            autoOpen: false,
            title: 'Pronostiques de vos amis',
            resizable: false,
            draggable: false,
            position: ['center',200],
            width: '70%',
            minWidth: 760,
            maxWidth: 900,
            modal: true
        });
    });
    
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

        for ($i = 0; $i < sizeof($groups); $i++) {
            echo '<li><a href="#groupe' . $groups[$i]->getId() . '">' . $groups[$i]->getTitle() . '</a></li>';
        }
        ?>
        <li><a href="#leaderbord">Classement</a></li>
    </ul>
    <?php
    foreach ($groups as $group) {
        $groupId = $group->getId();
        $gamesForThisGroup = getGamesForGroup($games, $groupId);

        echo '<div id="groupe' . $groupId . '">';
        foreach ($gamesForThisGroup as $game) {
            $teamA = getTeam($teams, $game->getTeam_a());
            $teamB = getTeam($teams, $game->getTeam_b());
            $bet = getBet($bets, $game->getId());
            $gameUTCTimestamp = strtotime($game->getStart_date()); // to display add 2h (7200sec) because FRANCE is GMT+2

            echo '<div class="match">
                            <span class="matchDate">' . strftime("%a %d/%m %H:%M", $gameUTCTimestamp + 7200) . '</span>
                            <span class="matchTeamA">' . $teamA->getName() . ' <img src="includes/pictures/flags/' . $teamA->getFlag() . '" alt="' . strtoupper($teamA->getFlag()) . '" /></span>';
            if ($currentUTCTimestamp < $gameUTCTimestamp + 7200) {
                displayBetFormular($game->getId(), $bet->getScore_a(), $bet->getScore_b());
            } else {
                displayBetResult($POINTS, $game->getId(), $game->getScore_a(), $game->getScore_b(), $bet->getScore_a(), $bet->getScore_b());
                displayFriendBets($POINTS, $friendsRanking, $game->getId(), $game->getScore_a(), $game->getScore_b());
            }
            echo '<span class="matchTeamB"><img src="includes/pictures/flags/' . $teamB->getFlag() . '" alt="' . strtoupper($teamB->getFlag()) . '" /> ' . $teamB->getName() . '</span>'
            . '</div>';
        }
        echo '</div>';
    }
    ?>

    <div id="leaderbord">
        <div class="scores ui-widget ui-widget-content ui-corner-all">
            <div class="widget-title ui-state-default ui-corner-all">Amis</div>
            <?php
            $formerScore = 0;
            $formerPosition = 0;
            for ($position = 0; $position < sizeof($friendsRanking['data']); $position++) {
                $userInfos = $friendsRanking['data'][$position]['user'];
                $score = $friendsRanking['data'][$position]['score'];
                if ($formerScore == $score) {
                    $displayPosition = $formerPosition;
                } else {
                    $displayPosition = $position + 1;
                }
                if ($userInfos['id'] == $user->getFacebookId()) {
                    $myscore = ' myscore';
                } else {
                    $myscore = '';
                }
                ?>
                <div class="score<?php echo $myscore; ?>">
                    <div class="rank"><span class="rank<?php echo $displayPosition; ?>"><?php echo $displayPosition; ?></span></div>
                    <img src="//graph.facebook.com/<?php echo $userInfos['id']; ?>/picture" class="friendPicture" alt="non dispo"/>
                    <div class="friendInfo">
                        <span class="bold"><?php echo $userInfos['name']; ?></span><br />
                        Score : <?php echo $score; ?>
                    </div>
                </div>
                <?php
                $formerScore = $score;
                $formerPosition = $displayPosition;
            }
            ?>
        </div>
        <div class="scores ui-widget ui-widget-content ui-corner-all">
            <div class="widget-title ui-state-default ui-corner-all ui-helper-clearfix">Général</div>

            <?php
            $userNumber = sizeof($usersRanking);
            $myposition = 0;
            $formerScore = 0;
            $formerPosition = 0;
            for ($position = 0; $position < sizeof($usersRanking); $position++) {
                $theUser = $usersRanking[$position];
                if ($formerScore == $theUser->getScore()) {
                    $displayPosition = $formerPosition;
                } else {
                    $displayPosition = $position + 1;
                }
                if ($position < 20 || $theUser->getFacebookId() == $user->getFacebookId() || $position > ($userNumber - 4)) {
                    if ($theUser->getFacebookId() == $user->getFacebookId()) {
                        $myscore = ' myscore';
                        $myposition = $position;
                    } else {
                        $myscore = '';
                    }
                    if ($position == ($userNumber - 3) && $position > 20 && $myposition > 20 && $myposition < ($userNumber - 4)) {
                        echo '<div class="scoreSeparator">...</div>';
                    }
                    ?>
                    <div class="score<?php echo $myscore; ?>">
                        <div class="rank"><span class="rank<?php echo $displayPosition; ?>"><?php echo $displayPosition; ?></span></div>
                        <img src="//graph.facebook.com/<?php echo $theUser->getFacebookId(); ?>/picture" class="friendPicture" alt="non dispo"/>
                        <div class="friendInfo">
                            <span class="bold"><?php echo $theUser->getFirst_name() . ' ' . $theUser->getLast_name(); ?></span><br />
                            Score : <?php echo $theUser->getScore(); ?>
                        </div>
                    </div>  
                    <?php
                }
                if ($position == 19) {
                    echo '<div class="scoreSeparator">...</div>';
                }
                $formerScore = $theUser->getScore();
                $formerPosition = $displayPosition;
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>

    <?php

    function getGamesForGroup($games, $groupId) {
        $gamesForThisGroup = array();
        foreach ($games as $game) {
            if ($game->getId_group() == $groupId) {
                array_push($gamesForThisGroup, $game);
            }
        }

        return $gamesForThisGroup;
    }

    function getTeam($teams, $teamId) {
        foreach ($teams as $team) {
            if ($team->getId() == $teamId) {
                return $team;
            }
        }
    }

    function getBet($bets, $gameId) {
        if ($bets != null) {
            foreach ($bets as $bet) {
                if ($bet->getMatch_id() == $gameId) {
                    return $bet;
                }
            }
        }
        return new Bet(null, null, null, null, null); // return an empty bet if none is found (i.e. user didn't bet on this match)
    }

    function displayBetFormular($gameId, $betScoreTeamA, $betScoreTeamB) {
        if ($betScoreTeamA == null && $betScoreTeamB == null) {
            $bet = 'Pari : ?';
        } else {
            $bet = 'Pari : ' . $betScoreTeamA . ' - ' . $betScoreTeamB;
        }

        echo '<span id="matchScore_' . $gameId . '" class="matchScore" title="Modifier le pari" onclick="modifyBet(' . $gameId . ')">' . $bet . '</span>
                  <span id="matchScoreInput_' . $gameId . '" class="matchScoreInput">
                    <select id="scoreA_match_' . $gameId . '" name="scoreA_match_' . $gameId . '">';
        displayPossibleScores($betScoreTeamA);
        echo '</select>
                        - 
                    <select id="scoreB_match_' . $gameId . '" name="scoreB_match_' . $gameId . '">';
        displayPossibleScores($betScoreTeamB);
        echo '</select>
                    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                            role="button" aria-disabled="false" onclick="saveBet(' . $gameId . ')" type="button">
                        <span class="ui-button-text">OK</span>
                    </button>
            </span>';
    }

    function displayPossibleScores($defaultValue) {
        for ($score = 0; $score <= 20; $score++) {
            if ($score == $defaultValue) {
                echo '<option value="' . $score . '" selected="selected">' . $score . '</option>';
            } else {
                echo '<option value="' . $score . '">' . $score . '</option>';
            }
        }
    }

    function displayBetResult($POINTS, $gameId, $scoreTeamA, $scoreTeamB, $betScoreTeamA, $betScoreTeamB) {
        if ($scoreTeamA == null && $scoreTeamB == null) {
            $betResult = 'En cours';
            $betResultClass = '';
            $point = '';
        } else {
            $betResult = 'Résultat : ' . $scoreTeamA . ' - ' . $scoreTeamB;
            if ($betScoreTeamA == null && $betScoreTeamB == null) {
                // user didn't bet
                $betResultClass = ' loose';
                $point = '<span class="points">= ' . $POINTS['lost'] . '</span>';
            } else if ($scoreTeamA == $betScoreTeamA && $scoreTeamB == $betScoreTeamB) {
                // user made a perfect bet
                $betResultClass = ' win';
                $point = '<span class="points"><span class="perfect">+ ' . $POINTS['perfect'] . '</span></span>';
            } else if (($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB)
                    || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB)
                    || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)) {
                // user made a winning bet
                $betResultClass = ' win';
                $point = '<span class="points">+ ' . $POINTS['win'] . '</span>';
            } else {
                // user made a loosing bet
                $betResultClass = ' loose';
                $point = '<span class="points">= ' . $POINTS['lost'] . '</span>';
            }
        }

        if ($betScoreTeamA == null && $betScoreTeamB == null) {
            $bet = 'Pari : Aucun';
        } else {
            $bet = 'Pari : ' . $betScoreTeamA . ' - ' . $betScoreTeamB;
        }
        ?>
        <script type="text/javascript">
            $(function() {
                $( "#showFriendsBet<?php echo $gameId; ?>" ).click(function() {
                    $( "#friendsBet<?php echo $gameId; ?>" ).dialog( "open" );
                    return false;
                });
            });        
        </script>
        <?php
        echo '<span id="showFriendsBet' . $gameId . '" class="matchScoreEnd' . $betResultClass . '" title="Voir les pronostiques de vos amis">' . $betResult . ' // ' . $bet . ' ' . $point . '</span>';
    }

    function displayFriendBets($POINTS, $friendsRanking, $gameId, $scoreTeamA, $scoreTeamB) {
        $allFriendIds = array();
        for ($position = 0; $position < sizeof($friendsRanking['data']); $position++) {
            array_push($allFriendIds, $friendsRanking['data'][$position]['user']['id']);
        }

        $friendBets = Bet::findAllBetsForUserIdsAndGameId($allFriendIds, $gameId);

        echo '<div id="friendsBet' . $gameId . '" class="showFriendsBetDialog">';
        foreach ($friendBets as $bet) {

            echo '<div class="match">'
            . '<span class="userName"></span>';

            $betScoreTeamA = $bet['score_a'];
            $betScoreTeamB = $bet['score_b'];
            if ($scoreTeamA == null && $scoreTeamB == null) {
                $betResultClass = '';
                $point = '';
            } else {
                if ($betScoreTeamA == null && $betScoreTeamB == null) {
                    // user didn't bet
                    $betResultClass = ' loose';
                    $point = '<span class="points">= ' . $POINTS['lost'] . '</span>';
                } else if ($scoreTeamA == $betScoreTeamA && $scoreTeamB == $betScoreTeamB) {
                    // user made a perfect bet
                    $betResultClass = ' win';
                    $point = '<span class="points"><span class="perfect">+ ' . $POINTS['perfect'] . '</span></span>';
                } else if (($scoreTeamA > $scoreTeamB && $betScoreTeamA > $betScoreTeamB)
                        || ($scoreTeamA == $scoreTeamB && $betScoreTeamA == $betScoreTeamB)
                        || ($scoreTeamA < $scoreTeamB && $betScoreTeamA < $betScoreTeamB)) {
                    // user made a winning bet
                    $betResultClass = ' win';
                    $point = '<span class="points">+ ' . $POINTS['win'] . '</span>';
                } else {
                    // user made a loosing bet
                    $betResultClass = ' loose';
                    $point = '<span class="points">= ' . $POINTS['lost'] . '</span>';
                }
            }


            echo '<span class="matchScoreEnd' . $betResultClass . '" style="text-align:left;min-width:280px;width:auto;padding-left:5px;">' . $bet['first_name'] . ' ' . $bet['last_name'] . ' : ' . $betScoreTeamA . ' - ' . $betScoreTeamB . ' ' . $point . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>
</div>

<?php

require 'settings/init.php';

// Only save the bet if user is connected
if (Session::getInstance()->isUserConnected()) {
    $currentUTCTimestamp = gmmktime();
    $game = Game::find($_POST['gameId']);

    // Only update bet if the match has not begun
    if ($currentUTCTimestamp < strtotime($game->getStart_date())) {
        $betForThisMatch = Bet::findBetByGameIdForUser($_POST['gameId'], Session::getInstance()->getUserSession());
        if ($betForThisMatch == null) {
            $bet = new Bet($_POST['gameId'], Session::getInstance()->getUserSession()->getId(), $_POST['scoreA'], $_POST['scoreB'], false);
            Bet::add($bet);
        } else {
            $betForThisMatch[0]->setScore_a($_POST['scoreA']);
            $betForThisMatch[0]->setScore_b($_POST['scoreB']);
            Bet::update($betForThisMatch[0]);
        }
    }
}
?>

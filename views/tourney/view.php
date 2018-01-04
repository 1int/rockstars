<?php
    use app\models\Tourney;

    /** @var $this yii\web\View */
    /** @var Tourney $tourney */
    $this->title = $tourney->team1name . ' vs ' . $tourney->team2name;
    $this->params['breadcrumbs'][] = ['label'=>'Team Battles', 'url'=>'/tourneys'];
    $this->params['breadcrumbs'][] = $tourney->team1name . ' vs ' . $tourney->team2name;
    $rounds = $tourney->getTotalRounds();
    $t = $tourney;
?>

    <header id="tourney-general-info">
        <div id="tourney-score">
            <span class="tourney-team"><img class="tourney-team-logo" src="<?=$t->team1logo?>"></span>
            <span class="tourney-info-vs tourney-score"><?=$t->getTotalScore()?></span>
            <span class="tourney-team"><img class="tourney-team-logo" src="<?=$t->team2logo?>"></span>
            <span class="tourney-finished">(Finished)</span>
        </div>
        <div class="well" id="tourney-general-info">
            <h4>Match details</h4>
            <table id="tourney-details">
                <tr><td>Status</td><td><?=$t->isFinished ? 'Finished' : 'Playing'?></td></tr>
                <tr><td>Date</td><td><?=$t->date?></td></tr>
                <tr><td>Time Control</td><td><?=$t->timeControlFullString?></td></tr>
                <tr><td>Team <?=$t->team1name?></td><td><?=$t->team1PlayersWithLinks?></td></tr>
                <tr><td>Team <?=$t->team2name?></td><td><?=$t->team2PlayersWithLinks?></td></tr>
            </table>
        </div>
    </header>


    <div id="games-title">Games</div>

<?php
    for($round = 1; $round <= $rounds; $round++) {
        print '<div class="round-number">Round '.  $round ;
        if(!$t->isRoundFinished($round)) {
            if($round == 1 || $t->isRoundFinished($round)) {
                print '<span class="round-playing">playing...</span>';
            }
        }

        print '<span class="round-score">[' . $t->getRoundScore($round) . ']</span>' . "\n";
        print '</div>' . "\n";
        $matches = $tourney->getMatchesOfRound($round);

        foreach($matches as $match) {
            $whiteWon = $match->result == 1;
            $blackWon = $match->result == -1;
            ?>
            <div class="tourney-match">
                <div class="match-content">
                    <?=$match->iframe?>
                </div>
                <span class="tourney-match-info">
                    <span class='<?=$whiteWon? 'player1 player-won':'player1'?>'>
                          <?=$match->white?>
                    </span>
                    <span class="tourney-vs">vs</span>
                    <span class='<?=$blackWon? 'player2 player-won':'player2'?>'>
                        <?=$match->black?>
                    </span>
                    <span class="tourney-match-score">
                        <?=$match->matchScore?>
                    </span>
                </span>
            </div>
<?php
        }
    }
?>
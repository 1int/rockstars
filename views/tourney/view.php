<?php
    use app\models\Tourney;
    use app\models\PlayerScore;
    use app\models\Member;

    /** @var $this yii\web\View */
    /** @var Tourney $tourney */
    $this->title = $tourney->team1name . ' vs ' . $tourney->team2name;
    $this->params['breadcrumbs'][] = ['label'=>'Team Battles', 'url'=>'/tourneys'];
    $this->params['breadcrumbs'][] = $tourney->team1name . ' vs ' . $tourney->team2name;
    $rounds = $tourney->getTotalRounds();
    $t = $tourney;

    /** @var Member $user */
    $user = Yii::$app->user->identity;
?>

    <header id="tourney-general-info">
        <div id="tourney-score">
            <span class="tourney-team"><img class="tourney-team-logo" src="<?=$t->team1logo?>"></span>
            <span class="tourney-info-vs tourney-score"><?=$t->getTotalScore()?></span>
            <span class="tourney-team"><img class="tourney-team-logo" src="<?=$t->team2logo?>"></span>
            <?php if( $t->isFinished) { ?>
            <span class="tourney-finished">(Finished)</span>
            <?php } ?>
        </div>
        <div class="well container-fluid" id="tourney-general-info">
            <div class="col-sm-4 col-sm-offset-2">
                <h4>Match details</h4>
                <table id="tourney-details">
                    <tr><td>Status</td><td><?=$t->isFinished ? 'Finished' : 'Playing'?></td></tr>
                    <tr><td>Date</td><td><?=$t->date?></td></tr>
                    <tr><td>Time Control</td><td><?=$t->timeControlFullString?></td></tr>
                    <tr><td><?=str_replace(' ', '&nbsp;', $t->team1name)?>&nbsp;players</td><td><?=$t->team1PlayersWithLinks?></td></tr>
                    <tr><td><?=str_replace(' ', '&nbsp;', $t->team2name)?>&nbsp;players</td><td><?=$t->team2PlayersWithLinks?></td></tr>
                </table>
            </div>
            <div class="col-sm-5 col-sm-offset-1" style="text-align: center">
                <table id="tourney-best-players" style="text-align: left">
                    <caption>Best players</caption>
                    <?php
                        /** @var PlayerScore $score */
                        foreach($t->bestPlayers as $score) {
                            printf("<tr><td><a href='https://lichess.org/@/%s' target='_blank'>%s</a></td><td>%s</td><td style='color: rgba(255,255,255,0.33)'>%f<td>\n",
                                $score->player, $score->player, $score->scoreString, $score->avgOpponentScore);
                        }
                    ?>
                </table>
            </div>
        </div>
    </header>


    <div id="games-title">Games</div>

<?php
    for($round = 1; $round <= $rounds; $round++) {
        print '<div class="round-number">Round '.  $round ;
        if(!$t->isRoundFinished($round)) {
            if($round == 1 || $t->isRoundFinished($round-1)) {
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

<?php if($user && $user->canManageTourneys() ) { ?>
<a id="btn-update-results" href="/tourney/update?id=<?=$t->id?>)" class="admin-button">Update Results</a>


<?php ob_start() ?>
$("#btn-update-results").detach().appendTo($("ul.breadcrumb"));
<?php $this->registerJs(ob_get_clean()); ?>
<?php } ?>

<?php
    use app\models\Tourney;
    use app\models\PlayerScore;

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
            <?php if( $t->isFinished) { ?>
            <span class="tourney-finished">(Finished)</span>
            <?php } ?>
        </div>
        <div class="well container-fluid" id="tourney-general-info">
            <div class="col-sm-6">
                <h4>Match details</h4>
                <table id="tourney-details">
                    <tr><td>Status</td><td><?=$t->isFinished ? 'Finished' : 'Playing'?></td></tr>
                    <tr><td>Date</td><td><?=$t->date?></td></tr>
                    <tr><td>Time Control</td><td><?=$t->timeControlFullString?></td></tr>
                    <tr><td><?=str_replace(' ', '&nbsp;', $t->team1name)?>&nbsp;players</td><td><?=$t->team1PlayersWithLinks?></td></tr>
                    <tr><td><?=str_replace(' ', '&nbsp;', $t->team2name)?>&nbsp;players</td><td><?=$t->team2PlayersWithLinks?></td></tr>
                </table>
            </div>
            <div class="col-sm-6">
                <h4>Best players</h4>
                <table id="tourney-best-players">
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

<a id="btn-update-results" href="javascript: void(0)" class="admin-button">Update Results</a>


<div id="modal-update-results" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Results</h4>
            </div>
            <form action="/tourney/update?id=<?=$t->id?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="admin-password">Admin password:</label>
                        <input id="admin-password" name="admin-password" class="form-control" type="password"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php ob_start() ?>
$("#btn-update-results").detach().appendTo($("ul.breadcrumb")).click(function() {
$("#modal-update-results").modal();
});;
<?php $this->registerJs(ob_get_clean()); ?>




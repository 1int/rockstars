<?php
use app\models\Tourney;
use app\models\Member;

/** @var $this yii\web\View */
/** @var Tourney[] $tourneys */

$this->title = 'Rockstars! — Team Battles';
$this->params['breadcrumbs'][] = 'Team Battles';


/** @var Tourney $lastTourney */
$lastTourney = Tourney::find()->where(1)->limit(1)->orderBy('id DESC')->one();
$lastId = $lastTourney->id;

?>
    <div id="tourney-list">
        <?php foreach($tourneys as $t) { ?>
            <div class="tourney">
                <a href="<?=$t->url?>">
                    <div class="tourney-inner-wrapper">
                        <div class="tourney-title">
                            <span class="team team1"><img class="team-logo" src="<?=$t->team1logo?>"><span class="team-name"><?=$t->team1name?></span></span>
                            <span class="vs">VS</span>
                            <span class="team team2"><img class="team-logo" src="<?=$t->team2logo?>"><span class="team-name"><?=$t->team2name?></span></span>
                        </div>
                        <div class="tourney-info">
                            <?=$t->date?> • <?=$t->timeControl?>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>

    <?php
        /** @var Member $user */
        $user = Yii::$app->getUser()->getIdentity();
        if($user && $user->canManageTourneys()) {
    ?>
    <a id="btn-create-tourney" href="javascript: void(0)" class="admin-button">New Team Battle</a>

<!-- Modal -->
<div id="modal-create-tourney" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New Team Battle</h4>
            </div>
            <form action="/tourney/new" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="team1-name">Team #1 name:</label>
                        <input id="team1-name" name="team1name" class="form-control" value="Rockstars!"/>
                    </div>
                    <div class="form-group">
                        <label for="team1-players">Team #1 players:</label>
                        <input id="team1-players" name="team1players" class="form-control" value="linto,kabacis,asuka2,GlebKl,Tatiana91"/>
                    </div>
                    <div class="form-group">
                        <label for="team1-logo">Team #1 logo:</label>
                        <input id="team1-logo" name="logo1" class="form-control" type="file"/>
                    </div>

                    <div class="form-group">
                        <label for="team2-name">Team #2 name:</label>
                        <input id="team2-name" name="team2name" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="team2-players">Team #2 players:</label>
                        <input id="team2-players" name="team2players" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="team2-logo">Team #2 logo:</label>
                        <input id="team2-logo" name="logo2" class="form-control" type="file"/>
                    </div>

                    <div class="form-group">
                        <label for="time-control">Time Control:</label>
                        <input id="time-control" name="time_control" class="form-control" value="3+2"/>
                    </div>

                    <div class="form-group">
                        <label for="new-tourney-date">Match date:</label>
                        <input id="new-tourney-date" name="date" class="form-control" type="date"/>
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug:</label>
                        <input id="slug" name="slug" class="form-control" value="tourney-<?=($lastId+1)?>"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                    <button type="submit" class="btn btn-primary">Create</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php ob_start() ?>
$("#btn-create-tourney").detach().appendTo($("ul.breadcrumb")).click(function() {
    $("#modal-create-tourney").modal();
});;
<?php $this->registerJs(ob_get_clean()); ?>
<?php } ?>
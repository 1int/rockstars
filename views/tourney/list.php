<?php
use app\models\Tourney;

/* @var $this yii\web\View */
/* @var Tourney[] $tourneys */
$this->title = 'Rockstars! — Team Battles';
$this->params['breadcrumbs'][] = 'Team Battles';
?>
<div class="site-about">
    <div id="tourney-list">
        <?php foreach($tourneys as $t) { ?>
            <div class="tourney">
                <div class="tourney-title">
                    <span class="team1"><img class="team-logo" src="<?=$t->team1logo?>"><span class="team-name"><?=$t->team1name?></span></span>
                    <span class="vs">VS</span>
                    <span class="team2"><img class="team-logo" src="<?=$t->team2logo?>"><span class="team-name"><?=$t->team2name?></h1></span>
                </div>
                <div class="tourney-info">
                    <?=$t->date?> • <?=$t->timeControl?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

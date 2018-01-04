<?php
use app\models\Tourney;

/* @var $this yii\web\View */
/* @var Tourney[] $tourneys */
$this->title = 'Rockstars! — Team Battles';
$this->params['breadcrumbs'][] = 'Team Battles';
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


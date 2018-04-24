<?php
    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     * @var \app\models\TacticsLevel[] $levels
     */


    $this->title = 'Rockstars  — Tactics Tests';
    $this->params['breadcrumbs'][] = 'Tactics Tests';
    //$this->params['breadcrumbs'][] = ['label'=>'Team Battles', 'url'=>'/tourneys'];
    //$this->params['breadcrumbs'][] = $tourney->team1name . ' vs ' . $tourney->team2name;
?>
<div id='tactics-level-list'>
<?php foreach($levels as $level) {
?>
    <a class="tactics-level" href="/tactics/<?=$level->slug?>">
        <div class="tactics-level">
            <div class="level-title"><?=$level->name?></div>
            <div class="level-description"><?=$level->description?></div>
        </div>
    </a>
<?php
    }
?>
</div>

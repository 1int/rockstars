<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level;
     * @var TacticsTest[] $tests;
     */

    $this->title = 'Rockstars  — Tactics: ' . $level->name;
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = $level->name;
    $i = 0;
?>
    <div id="tests-list">
        <!--<h4 id="level-title"><?=$level->name?></h4>-->
        <?php foreach($tests as $test) { $i++; if( $i > 3 ) { break; }?>
            <div class="col-sm-4 tactics-test"><a href="/tactics/<?=$level->slug?>/<?=$test->id?>" class="tactics-test">
            <?=$test->isFinishedByCurrentUser()? '<i class="glyphicon glyphicon-ok-circle"></i>' : (true ? '' : '<i class="glyphicon glyphicon-play-circle"></i>')?> Test <?=$test->id?> </a></div>
        <?php }?>
    </div>


    <?php ob_start();?>
        $("div.tactics-test").click(function(){
            window.location.href = $(this).find("a").attr("href");
            return false;
        });
    <?php $this->registerJs(ob_get_clean()); ?>

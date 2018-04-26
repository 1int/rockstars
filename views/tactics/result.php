<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    use app\models\TacticsTestResult;
    /**
     * Crafted by Pavel Lint 25/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level;
     * @var TacticsTest $test;
     * @var TacticsTestResult $result;
     * @var TacticsTestResult[] $highscores;
     */

    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number . ' results';

?>
<div id="score-content">
    <div class="col-lg-9">
        <h2 class="score-title">Your score</h2>
        <span id="your-result"><?=$result->score?></span>
        <!--<div id="tactics-comment">You are doing O.K.</div>-->
    </div>


    <div class="col-lg-3">
        <table id="tactics-best" style="text-align: left">
            <caption>Best&nbsp;results</caption>
            <tbody>
                <?php foreach($highscores as $r) { ?>
                    <tr><td><?=$r->player->usernameWithLink?></td><td><?=$r->score?></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
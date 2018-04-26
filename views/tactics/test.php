<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    use yii\web\View;
    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level
     * @var TacticsTest $test
     * @var int $timeLeft
     * @var bool $isStarted
     * @var View $this
     */


    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number;
?>


    <div id="start-test"><span>Start</span></div>
    <div id="countdown3" class="countdown" style="display: none">3</div>
    <div id="countdown2" class="countdown" style="display: none">2</div>
    <div id="countdown1" class="countdown" style="display: none">1</div>

    <p id="tactics-timer" style="display: none">10:00</p>

    <div id="test-container" style="display: none">
        <img id="img-position" src="" alt=""/>
        <div id="answer-holder">
            <input class="input form-control" id="answer" placeholder="Nf3"></input>
        </div>
        <button class="btn btn-large btn-primary" id="btn-prev" style="display: none"><i class="glyphicon glyphicon-circle-arrow-left"></i> Previous</button>
        <button class="btn btn-large btn-primary" id="btn-next">Next <i class="glyphicon glyphicon-circle-arrow-right"></i></button>
    </div>


<?php ob_start();?>
        $("div#start-test").click(function(){
            $(this).hide();
            startCountdown();
        });
        $("#tactics-timer").detach().appendTo($("ul.breadcrumb"));
        $("#btn-prev").click(previousPosition);
        $("#btn-next").click(nextPosition);
<?php $this->registerJs(ob_get_clean()); ?>

<?php ob_start();?>
        var timeLeft = <?=$timeLeft?>;
        var isStarted = <?=$isStarted ? 'true' : 'false'?>;
<?php $this->registerJs(ob_get_clean(), View::POS_BEGIN); ?>
<?php $this->registerJsFile('/js/tactics.js', ['position'=>View::POS_END]);

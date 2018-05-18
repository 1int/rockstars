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
     * @var yii\web\View $this
     */

    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number . ' results';

?>
<div id="score-content">
    <div id="result-answers" class="col-sm-3">
        <img id="answer-image" src="" alt="" style="display: none"/>
        <?php
            $uid = Yii::$app->user->getId();
            $i = 1;
            foreach($test->tacticsPositions as $p) {
                $answer = $p->answerForPlayer($uid);
                $isCorrect = $p->isAnswerCorrect($answer);
                $skip = $answer == null;
                printf("<div class='result-answer %s'>\n", $isCorrect ? "correct" : "wrong");
                if( $skip ) {
                    printf('<span><i class="glyphicon glyphicon-remove"></i> <p>%d.</p> — <b>%s</b></span>',
                                                                                            $i, $p->prettyAnswer);
                }
                else {
                   if( !$isCorrect ) {
                       printf('<span><i class="glyphicon glyphicon-remove"></i> <p>%d.</p> <s>%s</s> <b>%s</b></span>',
                           $i, $answer->answer, $p->prettyAnswer);
                   }
                   else {
                       printf('<span><i class="glyphicon glyphicon-ok"></i> <b><p>%d.</p> %s</b></span>', $i, $p->prettyAnswer);
                   }
                }
                print("</div>\n");
                $i++;
            }
        ?>
    </div>

    <div class="col-sm-6">
        <h2 class="score-title">Your score</h2>
        <span id="your-result"><?=$result->score?></span>
        <!--<div id="tactics-comment">You are doing O.K.</div>-->
    </div>


    <div class="col-sm-3">
        <table id="tactics-best" style="text-align: left">
            <caption>Best&nbsp;results</caption>
            <tbody>
                <?php foreach($highscores as $r) { ?>
                    <tr><td><img class="round" src="<?=$r->player->smallAvatar?>"></td><td><?=$r->player->usernameWithLink?></td><td><?=$r->score?></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<?php ob_start(); ?>
    function isMobile() {
        return ('ontouchstart' in document.documentElement && navigator.userAgent.match(/Mobi/));
    }
    if( !isMobile() ) {
        $("div.result-answer").mouseover(function() {
            $("#answer-image").show();
            var index = $(this).index();
            var url = window.location.href.replace("result", "image") + index.toString();
            $("#answer-image").attr("src", url);
        });

        $("#result-answers").mouseout(function() {
            $("#answer-image").hide();
        });
    }
    else {
        $("div.result-answer").click(function() {
            if( !$(this).hasClass("selected") ) {
                $("div.result-answer").removeClass("selected");
                $(this).addClass("selected");
                var index = $(this).index();
                var url = window.location.href.replace("result", "image") + index.toString();
                $("#answer-image").attr("src", url).show();
            }
            else {
                $(this).removeClass("selected");
                $("#answer-image").hide();
            }
        });
    }
<?php $this->registerJs(ob_get_clean());
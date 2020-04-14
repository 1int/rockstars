<?php
    /**
     * Crafted by Pavel Lint 04/06/2018
     * Mail to: pavel@1int.org
     */


    /** @var TacticsPosition $model */
    /** @var string $imgurl */
    /** @var View $this */
    /** @var int $total */

    $this->title = 'Rockstars! Admin — Test Options';
    $this->params['breadcrumbs'][] = 'Position ' . $model->id . ' of ' . $total;
    $this->params['homeLink'] = ['label'=>'Admin', 'url'=>'/admin'];

    use app\models\TacticsPosition;
    use yii\web\View;
    use yii\web\JqueryAsset;

    $verified = !!$model->verified;

?>


    <div id="verify-content-wrapper">
        <div id="verify-content">
            <div id="board" style="float: left"></div>

            <form action="" method="POST" style="margin-left: 35px; float: right">

                <div class="form-group">
                    <div>
                        <label><?=$model->dotdotdot ? 'Black' : 'White'?> to move</label>
                    </div>
                    <div>
                        <span>Correct Answer:</span> <label><?= $model->answer ?></label>
                    </div>
                    <div>
                        <span>Stockfish Answer:</span> <label><?= $model->stockfish_answer ?></label>
                    </div>
                </div>
                <div class="form-group">

                    <label class="col-form-label">Quiz Options:</label>

                    <div>
                        <div class="col-sm-5"
                        <label> 1:
                            <input class="input form-control" disabled="disabled" type="text" name="options[]" value="<?=$model->answer?>"/>
                        </label>
                    </div>
                </div>
                    <?php
                        $index = 2;
                        foreach($model->optionsArray() as $option) { ?>
                            <div>
                                <div class="col-sm-5 <?= ($index % 2 == 0) ? "col-sm-offset-2":""?>"
                                <label> <?=$index?>:
                                    <input class="input form-control" type="text" name="options[]" value="<?=$option?>"/>
                                </label>
                                </div>
                            </div>
                    <?php
                        $index++;
                        } ?>
                </div>

                <div style="clear: both"></div>
                    <div id="verify-buttons-holder" style="margin-top: 35px; margin-bottom: 25px">

                        <button type="submit" class="btn btn-success btn-block">Save</button>
                        <br/>
                        <?php if($model->id > 1) { ?>
                        <a class="btn btn-default" href="/admin/quiz-options/<?=(intval($model->id) - 1)?>"><i class="glyphicon glyphicon-chevron-left"></i></a>
                        <?php } ?>

                        <?php if($model->id < $total) { ?>
                        <a class="btn btn-default" href="/admin/quiz-options/<?=(intval($model->id) + 1)?>"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        <?php } ?>

                        <br/>
                        <br/>
                        <i style="font-size: 10px; color: #999">Last modified by @<?=$model->modifiedBy->username?></i>

                    </div>
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            </form>
        </div>
    </div>




    <script type="text/javascript">
        var fen = '<?=$model->fen?>';
        var positionId = <?=$model->id?>;
    </script>
    <link rel="stylesheet" href="/css/vendor/chessboard.min.css"/>
    <?php ob_start() ?>
    $(function() {
        var board = ChessBoard('board', fen);
    })
    <?php $this->registerJsFile('/js/vendor/chessboard.min.js', ['position'=>View::POS_END, 'depends' => [JqueryAsset::className()]]); ?>
    <?php $this->registerJs(ob_get_clean(), View::POS_END); ?>

<?php
    /**
     * Crafted by Pavel Lint 04/06/2018
     * Mail to: pavel@1int.org
     */


    /** @var TacticsPosition $model */
    /** @var string $imgurl */
    /** @var View $this */
    /** @var int $total */

    $this->title = 'Rockstars! Admin — Verify Recognition';
    $this->params['breadcrumbs'][] = 'Verify Position ' . $model->id . ' of ' . $total;
    $this->params['homeLink'] = ['label'=>'Admin', 'url'=>'/admin'];

    use app\models\TacticsPosition;
    use yii\web\View;
    use yii\web\JqueryAsset;

    $verified = !!$model->verified;

?>


    <div id="verify-content-wrapper">
        <div id="verify-content">
            <div id="verify-img">
                <img src="<?=$imgurl?>"/>
            </div>
            <div id="board"></div>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="fen" class="col-sm-2 col-form-label">FEN:</label>
                    <input type="text" name="fen" class="form-control" value="<?=$model->fen?>"/>
                </div>
                <label for="isBlackToMove">1. ...?</label>
                <input type="checkbox" <?=$model->dotdotdot? 'checked="checked"':''?> name="isBlackToMove"/>
                <div id="verify-buttons-holder">

                    <?php if($model->id > 1) { ?>
                    <a class="btn btn-default" href="/admin/verify/<?=(intval($model->id) - 1)?>"><i class="glyphicon glyphicon-chevron-left"></i></a>
                    <?php } ?>
                    <a class="btn btn-default" href="/admin/verify/<?=(intval($model->id) + 1)?>"><i class="glyphicon glyphicon-chevron-right"></i></a>
                    <?= $verified? '':'<button class="btn btn-primary" id="btn-recognize">Recognize</button>'?>
                    <button id="btn-recognized" class="btn btn-default" disabled="disabled"><i class="glyphicon glyphicon-ok"></i> Recognized</button>
                    <div class="loading-holder" style="display: none">
                        <div class="loader"></div>
                    </div>
                    <?= $verified?
                        '<button class="btn btn-default" disabled="disabled"><i class="glyphicon glyphicon-ok"></i> Verified</button>' :
                        '<button class="btn btn-primary">Verify</button>' ?>

                </div>
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            </form>
        </div>
    </div>




    <script type="text/javascript">
        var fen = '<?=$model->fen?>';
        var positionId = <?=$model->id?>;
    </script>
    <link rel="stylesheet" href="/css/chessboard.min.css"/>
    <?php ob_start() ?>
    $(function() {
        var board = ChessBoard('board', fen);
        $("#btn-recognize").click(function() {
            $(this).hide();
            $(".loading-holder").show();

            var fen = $("input[name=fen]").val();
            var check = $("input[type=checkbox]").is(":checked");
            $.post( '/admin/recognize/' + positionId, {fen: fen, check: check}, function(data) {
                $("input[name=fen]").val(data.fen);
                $("input[type=checkbox]").prop("checked", !!data.isBlackToMove);
                board.position(data.fen, true);
                $(".loading-holder").hide();
                $("#btn-recognized").show();
            }, 'JSON');
            return false;
        });
    })
    <?php $this->registerJsFile('/js/chessboard.min.js', ['position'=>View::POS_END, 'depends' => [JqueryAsset::className()]]); ?>
    <?php $this->registerJs(ob_get_clean(), View::POS_END); ?>

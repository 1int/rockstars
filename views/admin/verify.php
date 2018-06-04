<?php
    /**
     * Crafted by Pavel Lint 04/06/2018
     * Mail to: pavel@1int.org
     */


    /** @var TacticsPosition $model */
    /** @var string $imgurl */
    /** @var View $this */

    $this->title = 'Rockstars! Admin — Verify Recognition';
    $this->params['breadcrumbs'][] = 'Verify Position #' . $model->id;
    $this->params['homeLink'] = ['label'=>'Admin', 'url'=>'/admin'];

    use app\models\TacticsPosition;
    use yii\web\View;
    use yii\web\JqueryAsset;

?>


    <div id="verify-content">
        <div id="verify-img">
            <img src="<?=$imgurl?>"/>
        </div>
        <div id="board"></div>

        <script type="text/javascript">
            var fen = '<?=$model->fen?>';
        </script>
    </div>




    <link rel="stylesheet" href="/css/chessboard.min.css"/>
    <?php ob_start() ?>
    $(function() {
        var board = ChessBoard('board', fen);
    })
    <?php $this->registerJsFile('/js/chessboard.min.js', ['position'=>View::POS_END, 'depends' => [JqueryAsset::className()]]); ?>
    <?php $this->registerJs(ob_get_clean(), View::POS_END); ?>

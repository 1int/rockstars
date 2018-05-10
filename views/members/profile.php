<?php
    /**
     * Crafted by Pavel Lint 01/05/2018
     * Mail to: pavel@1int.org
     */

    use app\models\Member;
    use miloschuman\highcharts\Highcharts;
    use yii\web\View;
    use yii\web\JqueryAsset;
    use app\classes\lichess\Api;
    use app\models\NotableGame;

    /* @var $this yii\web\View */
    /* @var $member Member */
    /** @var array $scores */
    /** @var array $labels */
    /** @var $this View */

    $this->title = $member->name . ' @ Rockstars!';
    $owner = false;
    if( !Yii::$app->user->isGuest ) {
        $owner = Yii::$app->user->identity->getId() == $member->id;
    }

    $ownerClass = $owner ? ' class="owner"' : '';

    $member->updateLichessData(Api::getPlayersInfo([$member->username])[0]);


    //https://lichess.org/Ot9yJnMlMY4n
?>
    <div id="profile-wrapper">
        <div id="profile-left-column">
            <div id="avatar" <?=$ownerClass?>>
                <img id="avatar-image" src="<?=$member->avatar?>" <?=$ownerClass?>>
                <?php if($owner) { ?>
                <form id="avatar-form" action="" method="post" enctype="multipart/form-data">
                    <input type="file" id="avatar-input" name="avatar"/>
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                </form>
                <?php } ?>
                <span class="edit-link"></span>
            </div>
            <div>
                <h2 class="left-column-title">Ratings</h2>
                <span class="rating rating-title">Blitz</span>
                <span class="rating rating-value"><?=$member->rating_blitz?></span>
                <span class="rating rating-title">Bullet</span>
                <span class="rating rating-value"><?=$member->rating_bullet?></span>
                <span class="rating rating-title">Rapid</span>
                <span class="rating rating-value"><?=$member->rating_rapid?></span>
            </div>
            <div>
                <h2 class="left-column-title">Tactics Progress</h2>
                <?= Highcharts::widget([ 'id' => 'the-chart',
                'options' => [
                    'credits' => ['enabled' => false],
                    'legend'=> ['enabled' => false],
                    'chart' => [
                        'height' => 100,
                        'width' => 200,
                        'backgroundColor' => null,
                    ],
                    'title' => false,
                    'yAxis' => [
                        'max'   => 25,
                        'min' => 0,
                        'labels' => ['enabled'=>false],
                        'title' => false,
                       // 'tickInterval' => 5,
                        'lineWidth' => 0,
                        'minorGridLineWidth' => 0,
                        'lineColor' => 'transparent',
                        'minorTickLength' => 0,
                        'tickLength' => 0,
                        'gridLineColor' => 'transparent'
                    ],
                    'xAxis' => [
                       'labels' => ['enabled'=>false],
                        'categories' => $labels,
                        'lineWidth' => 0,
                        'minorGridLineWidth' => 0,
                        'lineColor' => 'transparent',
                        'minorTickLength' => 0,
                        'tickLength' => 0
                    ],
                    'series' => [
                        ['name' => 'Result', 'data' => $scores, 'labels'=>$labels ],
                    ],
                    'plotOptions' =>
                        ['series'=>
                         ['marker' => ['enabled'=>false]]
                        ],

                ]
                ]);?>
            </div>
        </div>

        <div id="profile-right-column">
            <h2 id="profile-title"><?=$member->name . ' (@' . $member->username . ')'?></h2>
            <div id="profile-description" <?=$ownerClass?>>
                <?=$member->bio != '' ? $member->bio : ($owner ? '(click to edit your bio)':'')?>
                <span class="edit-link"></span>
            </div>
            <div id="notable-games">
                <h2 class="section-title">Notable games <span class="button-add" id="add-game" data-toggle="modal" data-target="#modal-add-game"><i class="glyphicon glyphicon-plus"></i></span></h2>
                <div>
                    <?php $i = 0; foreach($member->notableGames as $game) {
                            /** @var NotableGame $game */ $i++; ?>
                        <div class="notable-game-container">
                            <div class="notable-game">
                                  <?=$game->getIframe()?>
                            </div>
                            <span class="nb-description">
                                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
                            </span>
                        </div>

                    <?php if( $i % 2 == 0 ) print "<br/>"; } ?>

                </div>
            </div>
            <div id="upcoming-events">
                <h2 class="section-title">Upcoming events by <?=$member->name?></h2>
            </div>
            <div id="private-contacts">
                <h2 class="section-title">Private contacts</h2>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div id="modal-add-game" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add new notable game</h4>
                </div>
                <form action="" method="POST" id="frm-add-game">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="gameurl">Lichess game url:</label>
                            <input id="gameurl" name="gameurl" class="form-control" placeholder="https://lichess.org/Ot9yJnMl"/>
                        </div>
                    <div class="modal-footer">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                        <button type="button" class="btn btn-primary" id="btn-add-game">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $this->registerJsFile('/js/profile.js', ['position'=>View::POS_END, 'depends' => [JqueryAsset::className()]]); ?>

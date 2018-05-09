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
    use app\classes\lichess\Player;

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

    $player = Api::getPlayersInfo([$member->username])[0];
    $member->updateLichessData($player);
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
                <span class="rating rating-value"><?=$player->getBlitzRating()?></span>
                <span class="rating rating-title">Bullet</span>
                <span class="rating rating-value"><?=$player->getBulletRating()?></span>
                <span class="rating rating-title">Rapid</span>
                <span class="rating rating-value"><?=$player->getRapidRating()?></span>
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
                <h2 class="section-title">Notable games</h2>
            </div>
            <div id="upcoming-events">
                <h2 class="section-title">Upcoming events by <?=$member->name?></h2>
            </div>
            <div id="private-contacts">
                <h2 class="section-title">Private contacts</h2>
            </div>
        </div>
    </div>

    <?php $this->registerJsFile('/js/profile.js', ['position'=>View::POS_END, 'depends' => [JqueryAsset::className()]]); ?>

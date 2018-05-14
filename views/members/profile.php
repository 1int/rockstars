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
    use app\models\Event;

    /* @var $this yii\web\View */
    /* @var $member Member */
    /** @var array $scores */
    /** @var array $labels */
    /** @var $this View */
    /** @var Event[] $events */

    $this->title = $member->name . ' @ Rockstars!';
    $owner = false;
    if( !Yii::$app->user->isGuest ) {
        $owner = Yii::$app->user->identity->getId() == $member->id;
    }

    $ownerClass = $owner ? ' class="owner"' : '';

    //$member->updateLichessData(Api::getPlayersInfo([$member->username])[0]);
?>
    <div id="profile-wrapper">
        <div id="profile-left-column">
            <div id="avatar" <?=$ownerClass?>>
                <img id="avatar-image" src="<?=$member->avatarUrl?>" <?=$ownerClass?>>
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
                        'type' => 'area'
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
                    'plotOptions' =>  [
                        'series'=>  [
                            'marker' => ['enabled'=>false],
                             'fillOpacity' => 0.25,
                        ],
                    ],
                ]]);?>
            </div>
        </div>



        <div id="profile-right-column">
             <span id="profile-stars">
                    <img src="/images/star-on.svg"/>
                    <img src="/images/star-on.svg"/>
                    <img src="/images/star-on.svg"/>
                    <img src="/images/star-on.svg"/>
                    <img src="/images/star-off.svg"/>
                </span>
            <h2 id="profile-title"><?=$member->name . ' (@' . $member->username . ')'?></h2>

            <div id="profile-description" <?=$ownerClass?>>
                <?=$member->bio != '' ? $member->bio : ($owner ? '(click to edit your bio)':'')?>
                <span class="edit-link"></span>
            </div>
            <div id="notable-games">
                <h2 class="section-title">Notable games <span class="button-add <?=$owner?'owner':''?>" id="add-game" data-toggle="modal" data-target="#modal-add-game"><i class="glyphicon glyphicon-plus"></i></span></h2>
                <div id="notable-games-container">
                    <?php if( count($member->notableGames) > 0 ) { ?>
                        <span>Click the game and use ← → keys to navigate</span>
                        <?php $i = 0; foreach($member->notableGames as $game) {
                            /** @var NotableGame $game */ $i++; ?>
                            <div class="notable-game-container <?=$owner?"owner":""?>" data-gid="<?=$game->id?>">
                                <div class="notable-game">
                                    <?=$game->getIframe()?>
                                </div>
                            <span class="nb-description">
                                <?=$game->description?>
                            </span>
                                <i class="btn-close glyphicon glyphicon-remove"></i>
                            </div>

                            <?php /*if( $i % 2 == 0 ) print "<br/>"; */ } ?>
                    <?php } else {
                        if (!$owner) {
                            print '<span>' . $member->name . ' has not added any games yet.</span>';
                        }
                        else {
                            print '<span>' . 'You have not added any games yet. Add a game to tell the world a little bit about your play.</span>';
                        }
                    }
                    ?>

                </div>
            </div>
            <div id="upcoming-events">
                <h2 class="section-title">Upcoming events by <?=$member->name?></h2>
                <div id="upcoming-events-list">
                    <?php if(count($events) == 0) { ?>
                        <span>No events</span>
                    <?php } else  {
                        foreach($events as $e) {
                            /** @var Event $e */
                            ?>
                            <div class="upcoming-event">
                                <div class="upcoming-event-date">
                                    <p><?=$e->dateOfMonth?></p>
                                    <p><?=$e->month?></p>
                                    <comment></comment>
                                </div>
                                <img src="<?=$e->image?>" alt="<?=$e->name?>">

                                <div class="upcoming-event-info">
                                    <h2><?=$e->name?></h2>
                                    <span><?=$e->dayOfWeek?>, <a href="http://www.thetimezoneconverter.com/?t=<?=$e->startTime?>&amp;tz=Moscow" title="See the time in your local timezone" target="_blank"><?=$e->startTime?></a>
                                       </span>
                                </div>
                            </div>
                        <?php }}?>
                </div>
            </div>
            <div id="private-contacts">
                <h2 class="section-title">Contacts</h2>
                <div id="private-contacts-list">

                    <div>
                        <label>Lichess:</label>
                        <span class="contact"><a href="<?=$member->link?>" target="_blank"><?=$member->link?></a></span>
                    </div>
                    <?php if($owner) { ?>
                    <form action="" method="post">
                        <div>
                            <label for="private-email">Email*:</label>
                            <input class="form-control" type="email" name="private-email" id="private-email" placeholder="xxxxx@xxxxx.xx"/>
                        </div>
                        <div>
                            <label for="private-email">Phone*:</label>
                            <input class="form-control" name="private-phone" id="private-phone" placeholder="+x (xxx) xxx-xx-xx"/>
                        </div>
                        <div>
                            <a href="javascript: void(0)" data-toggle="modal" data-target="modal-password">Change password</a>
                            <hr/>
                            <span class="hint">*only you can see this</span>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </form>
                    <?php } ?>
                </div>
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
                            <input id="gameurl" name="gameurl" class="form-control" placeholder="https://lichess.org/Ot9yJnMl" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="gamedesc">Say a few words about this game:</label>
                            <textarea rows="7" id="gamedesc" name="gamedesc" class="form-control" required="required"></textarea>
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

<?php
    /**
     * Crafted by Pavel Lint 09/11/2017
     * Mail to: pavel@1int.org
     */

    /* @var $this yii\web\View */
    /* @var $events Event[] */

    $this->title = 'Rockstars! — Schedule';

    use app\models\Event;

?>
<div id="events-index">

    <div class="row event-header">
        <h2 id="upcoming-events">Upcoming events</h2>
    </div>

    <?php $i=0; foreach($events as $e) { ?>
        <div class="row event <?= $i++ == 0 ? 'first-event':''?>">
            <div class="event-date">
                <p><?=$e->dateOfMonth?></p>
                <p><?=$e->month?></p>
                <comment></comment>
            </div>
            <img alt="<?=$e->name?>" src="<?=$e->image?>"/>

            <div class="event-info">
                <h2><?=$e->name?></h2>
                <span><?=$e->dayOfWeek?>, <a href="http://www.thetimezoneconverter.com/?t=<?=$e->startTime?>&amp;tz=Moscow" title="See the time in your local timezone" target="_blank"><?=$e->startTime?></a>

                 <?php if($e->master) { ?>
                    by <?=$e->master->nameWithLink;?>
                 <?php } ?>
                </span>
                <?php if($e->hasPairings) { ?>
                    <div class="pairings">
                        Pairs: <?=$e->pairings;?>
                    </div>
                <?php } ?>
            </div>
            <hr/>
        </div>
    <?php }?>
    <div class="row event-header">
        <comment>*time is in GMT+3. Click the time to convert to local time zone.</comment>
    </div>

</div>

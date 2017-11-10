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
                <span><?=$e->dayOfWeek?>, <?=$e->startTime?> by <a href="<?=$e->master->link?>" target="_blank"><?=$e->master->name?></a></span>
            </div>
            <hr/>
        </div>
    <?php }?>
    <div class="row event-header">
        <comment>*time is in GMT+3</comment>
    </div>

</div>

<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111959474-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-111959474-1');
    </script>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<img id="logo" src="/images/logo2.jpg"/><a id="brand" class="navbar-brand" href="/"> ' . Yii::$app->name . '</a>',
        'brandUrl' => Yii::$app->homeUrl,
       //'brandImage' => '/images/logo2.jpg',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    ?>
    <?php
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Members', 'url' => ['/members'], 'active'=>Yii::$app->controller->id == 'members'],
            ['label' => 'Ratings', 'url' => ['/rating'], 'active'=>Yii::$app->controller->id == 'rating',
                'items' => [
                    ['label' => 'Blitz', 'url' => ['/rating/blitz'], 'active'=>Yii::$app->controller->id == 'rating' && Yii::$app->requestedAction->id=='blitz'],
                   // ['label' => 'Rapid', 'url' => ['/rating/rapid'], 'active'=>Yii::$app->controller->id == 'rating' && Yii::$app->requestedAction->id=='classical'],
                    //['label' => 'Bullet', 'url' => ['/rating/bullet'], 'active'=>Yii::$app->controller->id == 'rating' && Yii::$app->requestedAction->id=='bullet'],
                ],
            ],
            ['label' => 'Schedule', 'url' => ['/schedule'], 'active'=>Yii::$app->controller->id == 'schedule'],
            ['label' => 'Team Battles', 'url' => ['/tourney'], 'active'=>Yii::$app->controller->id == 'tourney'],
            ['label' => 'About', 'url' => ['/site/about'], 'active'=>Yii::$app->controller->id == 'site'],
        ],
        'activateItems' => false
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Rockstars! <?= date('Y') ?></p>
        <p class="pull-right">
            <span id="radio-title">We listen to</span>
            <img src="/images/swh.png" alt="Radio SWHROCK" id="image-swh"/>
            <audio controls id="radio">
                <source src="http://87.110.219.34:8000/rockmp3" type="audio/mpeg">
            </audio>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

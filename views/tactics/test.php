<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level;
     * @var TacticsTest $test;
     */

    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number;

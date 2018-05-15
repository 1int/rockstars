<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use Yii;
    use app\models\Event;

    class ScheduleController extends BaseController
    {
        /**
         * @return string
         */
        public function actionIndex()
        {
            return $this->render('schedule-view', ['events'=>Event::GetUpcomingEventsList()]);
        }
    }
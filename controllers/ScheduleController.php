<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use app\models\Event;
    use app\models\Holiday;
    use \DateTime;

    class ScheduleController extends Controller
    {
        /**
         * @return string
         */
        public function actionIndex()
        {
            return $this->render('schedule-view', ['events'=>Event::GetUpcomingEventsList()]);
        }
    }
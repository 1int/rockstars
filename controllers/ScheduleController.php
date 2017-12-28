<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use app\models\Event;
    use \DateTime;

    class ScheduleController extends Controller
    {
        /**
         * @return string
         */
        public function actionIndex()
        {
            // How many times one event can show up in the list
            $repeatEventsLimit = 3;
            // How many total events are shown
            $totalEventsLimit = 8;


            /** @var Event[] $ret */
            $ret = [];


            /** @var Event[] $events */
            $events = Event::find()->where('repeatsInDays > 0 AND active = 1')->all();


            $today = new DateTime();
            foreach( $events as $e) {
                /** @var Event $e */
                $thisEventLimit = $repeatEventsLimit;
                while($thisEventLimit > 0 || $e->date < $today) {
                    if( $e->date >= $today ) {
                        $ret[] = $this->cloneEvent($e);
                        $thisEventLimit--;
                    }
                    $e->setDateToNextTime();
                }
            }

            $events = Event::find()->where('repeatsInDays = 0 AND active = 1')->all();
            foreach($events as $e) {
                $ret[] = $e;
            }


            usort($ret, function($a, $b){
                /** @var Event $a */
                /** @var Event $b */
                return $a->getTimestamp() > $b->getTimestamp();
            });


            if( count($ret) > $totalEventsLimit ) {
                $ret = array_chunk($ret, $totalEventsLimit)[0];
            }

            return $this->render('schedule-view', ['events'=>$ret]);
        }


        /**
         * @param Event $source
         * @return Event
         */
        protected function cloneEvent($source) {
            $ret = new Event();
            $ret->attributes = $source->attributes;
            $ret->setTimestamp($source->getTimestamp());
            $ret->populateRelation('master', $source->master);
            return $ret;
        }
    }
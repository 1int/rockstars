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
            // How many times one event can show up in the list
            $repeatEventsLimit = 50;
            // How many total events are shown
            $totalEventsLimit = 10;


            /** @var Event[] $ret */
            $ret = [];


            /** @var Event[] $events */
            $events = Event::find()->where('repeatsInDays > 0 AND active = 1')->all();



            $today = new DateTime();
            // dirty timezone hack, I'm sorry
            $today->add(\DateInterval::createfromdatestring('+3 hours'));

            $todayString = $today->format('Y-d-m');
            $thresholdDate = new DateTime();
            $thresholdDate->sub(new \DateInterval('P365D'));
            $thresholdString = $thresholdDate->format('Y-d-m');

            foreach( $events as $e) {
                /** @var Event $e */
                $thisEventLimit = $repeatEventsLimit;
                while($thisEventLimit > 0 || $e->date < $thresholdDate) {
                    if( $e->date >= $thresholdDate ) {
                        $ret[] = $this->cloneEvent($e);
                        $thisEventLimit--;
                    }
                    $e->setDateToNextTime();
                }
            }

            $events = Event::find()->where("repeatsInDays = 0 AND active = 1 AND start >= '$todayString'")->all();
            foreach($events as $e) {
                $ret[] = $e;
            }


            usort($ret, function($a, $b){
                /** @var Event $a */
                /** @var Event $b */
                return $a->getTimestamp() > $b->getTimestamp();
            });



            // Now check if some of the events should be cancelled due to holidays
            $holidays = Holiday::find()->where("date >= '{$thresholdString}'")->all();

            /** @var Holiday $holiday */
            foreach($holidays as $holiday) {
                for ($i = 0; $i < count($ret); $i++) {
                    $dateString = $ret[$i]->date->format('Y-m-d');
                    if($dateString == $holiday->date) {
                        for($j = $i; $j < count($ret) - 1; $j++) {
                            $ret[$j]->assignDate($ret[$j+1]);
                        }
                        unset($ret[count($ret)-1]);
                    }
                }
            }

            $ret = array_filter($ret, function($e) use ($today) {
                /** @var Event $e */
                return $e->date >= $today;
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
            $ret->id = $source->id;
            $ret->setTimestamp($source->getTimestamp());
            $ret->populateRelation('master', $source->master);
            return $ret;
        }
    }
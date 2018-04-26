<?php
    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\TacticsAnswer;
    use app\models\TacticsLevel;
    use app\models\TacticsTest;
    use app\models\TacticsTestResult;
    use app\models\Member;

    use yii\web\Controller;
    use yii\web\NotFoundHttpException;
    use Yii;

    class TacticsController extends Controller {


        /** @var  TacticsLevel $level */
        protected $level;
        /** @var  TacticsTest $test */
        protected $test;

        public function actionIndex() {
            $levels = TacticsLevel::find()->all();
            return $this->render('index', ['levels'=>$levels]);
        }

        public function actionLevel($slug) {
            if( Yii::$app->user->isGuest ) {
                Yii::$app->user->setReturnUrl('/tactics/' . $slug);
                Yii::$app->user->loginRequired();
            }
            $level = TacticsLevel::findBySlug($slug);
            if( $level == null ) {
                throw new NotFoundHttpException();
            }
            return $this->render('tests-list', ['level' => $level, 'tests'=>$level->publishedTests]);
        }

        public function actionTest($level, $test) {
            if( Yii::$app->user->isGuest ) {
                throw new NotFoundHttpException();
            }
            $this->loadEntities($level, $test);
            if( !$this->test->published ) {
                throw new NotFoundHttpException();
            }
            /** @var Member $user */
            $user = Yii::$app->user->identity;
            if($user->hasFinishedTest($this->test)) {
                return $this->redirect('/tactics/' . $this->level->slug . '/' . $this->test->id . '/result');
            }

            if( $this->test->isInProgressFor($user->id) ) {
                $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:uid',
                    ['test_id'=>$this->test->id, 'uid'=>$user->id])->one();
                /** @var TacticsTestResult $result */
                $timeLeft = intval(60 * $this->test->level->time - (time() - doubleval($result->start)));
                $isStarted = true;
            }
            else {
                $timeLeft = intval($this->test->level->time * 60);
                $isStarted = false;
            }

            return $this->render('test', ['test'=>$this->test, 'level'=>$this->level, 'timeLeft'=>$timeLeft, 'isStarted'=>$isStarted]);
        }

        public function actionResult($level, $test) {
            if( Yii::$app->user->isGuest ) {
                throw new NotFoundHttpException();
            }
            $this->loadEntities($level, $test);
            $userId = Yii::$app->user->identity->getId();


            $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:player_id',
                ['test_id'=>$this->test->id, 'player_id'=>$userId])->one();

            if( !$result ) {
                throw new NotFoundHttpException();
            }

            $highscores = TacticsTestResult::find()->where('test_id=:test_id',
                 ['test_id'=>$this->test->id])->orderBy('score DESC')->all();

            return $this->render('result', ['test'=>$this->test, 'level'=>$this->level, 'result'=>$result, 'highscores'=>
                                            $highscores]);
        }

        public function actionImage($level, $test, $position) {
            if( Yii::$app->user->isGuest ) {
                throw new NotFoundHttpException();
            }
            $this->loadEntities($level, $test);


            if( $position == 0 || !$this->test->isInProgressFor(Yii::$app->user->getId())) {
                $path =  Yii::getAlias('@app') . '/assets/tactics/default.jpeg';
            }
            else {
                $path = Yii::getAlias('@app') . '/assets/tactics/test' . $this->test->id . '_' . $position . '.jpeg';
            }

            if( !file_exists($path) ) {
                $path =  Yii::getAlias('@app') . '/assets/tactics/default.jpeg';
            }

            header('Content-Type: image/jpeg');
            readfile($path);
        }

        public function actionStart($level, $test) {
            if( Yii::$app->user->isGuest ) {
                throw new NotFoundHttpException();
            }
            $this->loadEntities($level, $test);
            $userId = Yii::$app->user->identity->getId();

            $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:player_id',
                ['test_id'=>$this->test->id, 'player_id'=>$userId])->one();
            if( $result != null ) {
                return "ok";
            }

            $result = new TacticsTestResult();
            $result->start = time();
            $result->player_id = $userId;
            $result->test_id = $this->test->id;
            $result->score = 0;
            if($result->save()) {
                return "started " . time();
            }
            else {
                return "error: " . print_r($result->getErrors(), true);
            }
        }

        public function actionAnswer($level, $test) {
            if (Yii::$app->user->isGuest) {
                throw new NotFoundHttpException();
            }
            $this->loadEntities($level, $test);
            $userId = Yii::$app->user->identity->getId();
            $text = Yii::$app->request->post("answer");
            $position_index = Yii::$app->request->post('position');
            $position_id = 12*($this->test->number-1) + $position_index + 1;

            $answer = TacticsAnswer::find()->where('player_id=:uid AND position_id=:position_id',
                ['uid'=>$userId, 'position_id'=>$position_id])->one();
            if( !$answer ) {
                $answer = new TacticsAnswer();
                $answer->test_id = $this->test->id;
                $answer->position_id = $position_id;
                $answer->player_id = $userId;
            }
            $answer->answer = $text;

            if($answer->save()) {
                return "ok";
            }
            else {
                return "error: " . print_r($answer->getErrors(), true);
            }
        }

        public function actionFinish($level, $test) {
            if (Yii::$app->user->isGuest) {
                throw new NotFoundHttpException();
            }
            $this->loadEntities($level, $test);
            $userId = Yii::$app->user->identity->getId();
            $this->test->finish($userId);
            print "ok";
        }

        protected function loadEntities($level, $test) {
            $this->level = TacticsLevel::findBySlug($level);
            if( $this->level == null ) {
                throw new NotFoundHttpException();
            }
            $this->test = TacticsTest::findOne($test);
            if( $this->test == null ) {
                throw new NotFoundHttpException();
            }
        }


    }
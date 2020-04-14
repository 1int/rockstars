<?php
    /**
     * Crafted by Pavel Lint 24/05/2018
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\classes\smsru\Smsru;
    use Yii;
    use app\models\Member;
    use yii\base\Action;
    use yii\helpers\Json;
    use yii\web\HttpException;
    use app\models\TacticsPosition;
    use yii\web\NotFoundHttpException;
    use app\models\TacticsTest;
    use app\commands\TacticsController as ConsoleCommand;

    class AdminController extends BaseController {

        /**
         * @param Action $action the action to be executed.
         * @return bool
         * @throws \yii\web\ForbiddenHttpException
         */
        public function beforeAction($action) {
            if( parent::beforeAction($action) ) {
                if( Yii::$app->user->isGuest ) {
                    Yii::$app->user->loginRequired();
                    return false;
                }
                return true;
            }
            else {
                return false;
            }
        }

        /**
         * @return string
         * @throws HttpException
         */
        public function actionSms() {

            /** @var Member $member */
            $member = Yii::$app->user->identity;
            if( !$member->canSendSms() ) {
                throw new HttpException(403, 'What the hell are you doing here. You dont belong here.');
            }

            if (Yii::$app->request->isPost) {

                $msg = Yii::$app->request->post('smstext');

                if( $msg ) {
                    $smsru = new Smsru(Yii::$app->params['smskey']);

                    $data = new \stdClass();
                    $data->multi = [ ];

                    $members = Member::find()->where('receive_sms=1')->all();

                    foreach ($members as $m) {
                        /** @var Member $m */
                        if ($m->phone) {
                            $data->multi[$m->phone] = $msg;
                        }
                    }

                    $request = $smsru->send($data);
                    if ($request->status == "OK") { // Запрос выполнен успешно
                        foreach ($request->sms as $phone => $sms) { // Перебираем массив отправленных сообщений
                            if ($sms->status == "OK") {
                                Yii::$app->session->addFlash('success', "Сообщение на номер $phone отправлено успешно. ");
                            }
                            else {
                                Yii::$app->session->addFlash('error', "Сообщение на номер $phone не отправлено.\n Текст ошибки: $sms->status_text. ");
                            }
                        }
                    }
                    else {
                        Yii::$app->session->addFlash('error', "Ошибка запроса: $request->status_text. ");
                    }
                }
            }

            return $this->render('sms');
        }

        /**
         * @return string
         * @throws HttpException
         */
        public function actionAnswers() {

            /** @var Member $member */
            $member = Yii::$app->user->identity;
            if( !$member->canInputAnswers() ) {
                throw new HttpException(403, 'You cannot do this to me');
            }

            if( Yii::$app->request->isPost ) {
                $test_id = Yii::$app->request->post('test_id');
                $answers = Yii::$app->request->post('answers');
                $points = Yii::$app->request->post('points');


                for($i = 0; $i < 12; $i++) {
                    $t = new TacticsPosition();
                    $t->test_id = $test_id;
                    $t->answer = $answers[$i];
                    $t->points = intval($points[$i]);
                    $t->dotdotdot = Yii::$app->request->post('dot' . $i) == 'on' ? 1 : 0;
                    if( !$t->save() ) {
                        Yii::$app->session->addFlash('error', print_r($t->getErrors(), true));
                    }
                }
                Yii::$app->session->addFlash('success', 'Test #' . $test_id . ' is saved!');

            }

            return $this->render('answers');
        }

        public function actionVerify($positionId) {
            /** @var Member $member */
            $member = Yii::$app->user->identity;
            if( !$member->canInputAnswers() ) {
                throw new HttpException(403, 'You cannot do this');
            }

            /** @var TacticsPosition $model */
            $model = TacticsPosition::findOne($positionId);
            if( !$model ) {
                throw new NotFoundHttpException('Position doesnt exist');
            }

            $total = TacticsPosition::find()->count();

            // Verify the recognition
            if( Yii::$app->request->isPost ) {
                $model->fen = Yii::$app->request->post('fen');
                $model->dotdotdot = Yii::$app->request->post('isBlackToMove') ? 1 : 0;
                $model->verified = 1;
                if($model->save()) {
                    return $this->redirect(['admin/verify', 'positionId' => intval($positionId) + 1]);
                }
                else {
                    foreach($model->getErrors() as $error) {
                        Yii::$app->session->addFlash('error', $error[0]);
                    }
                }
            }

            /** @var TacticsTest $test */
            $testId = $model->test->id;
            $levelId = $model->test->level_id;
            $pos = intval($positionId) % 12;
            if( $pos == 0 ) {
                $pos = 12;
            }
            $imgurl = '/tactics/' . $levelId . '/' . $testId . '/image' . $pos;

            return $this->render('verify', ['model'=>$model, 'imgurl'=>$imgurl, 'total'=>$total]);
        }

        public function actionRecognize($positionId) {

            /** @var Member $member */
            $member = Yii::$app->user->identity;
            if( !$member->canInputAnswers() ) {
                throw new HttpException(403, 'You cannot do this');
            }

            /** @var TacticsPosition $model */
            $model = TacticsPosition::findOne($positionId);
            if( !$model ) {
                throw new NotFoundHttpException('Position doesnt exist');
            }

            /** @var TacticsTest $test */
            $testId = $model->test->id;
            $levelId = $model->test->level_id;
            $pos = intval($positionId) % 12;
            if( $pos == 0 ) {
                $pos = 12;
            }

            $c = new ConsoleCommand('cid', Yii::$app);
            $c->level = $levelId;
            $c->runAction('recognize-one', [$testId, $pos]);

            $model = TacticsPosition::findOne($positionId);
            return Json::encode(['fen'=>$model->fen, 'isBlackToMove'=>$model->dotdotdot]);
        }

        public function actionIndex() {
            /** @var Member $member */
            $member = Yii::$app->user->identity;
            if( $member->role != Member::ADMIN ) {
                throw new HttpException(403, 'Access denied');
            }

            return $this->render('index');
        }

        /**
         * @param int $positionId
         * @return string
         * @throws HttpException
         * @throws NotFoundHttpException
         */
        public function actionOptions($positionId)
        {
            /** @var Member $member */
            $member = Yii::$app->user->identity;


            if( !$member->canInputOptions() ) {
                throw new HttpException(403, 'You cannot do this');
            }

            /** @var TacticsPosition $model */
            $model = TacticsPosition::findOne($positionId);
            if( !$model ) {
                throw new NotFoundHttpException('Position doesnt exist');
            }

            $total = TacticsPosition::find()->count();

            // Verify the recognition
            if( Yii::$app->request->isPost ) {
                $options = Yii::$app->request->post('options');

                foreach($options as $option) {
                    if($option == $model->answer) {
                        Yii::$app->session->addFlash('error', 'Ход ' . $option . ' правильный, нельзя указать его как неправильный вариант');
                        return $this->redirect(['admin/options', 'positionId' => intval($positionId)]);
                    }
                }
                
                $model->options = implode(" ", $options);
                $model->save(false);
                return $this->redirect(['admin/options', 'positionId' => intval($positionId) + 1]);
            }

            return $this->render('variants', ['model'=>$model, 'total'=>$total]);
        }

    }
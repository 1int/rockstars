<?php
    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\TacticsLevel;
    use app\models\TacticsTest;
    use yii\web\Controller;
    use yii\web\HttpException;
    use yii\web\NotFoundHttpException;

    class TacticsController extends Controller {


        public function actionIndex() {
            $levels = TacticsLevel::find()->all();
            return $this->render('index', ['levels'=>$levels]);
        }

        public function actionLevel($slug) {
            $level = TacticsLevel::findBySlug($slug);
            if( $level == null ) {
                throw new NotFoundHttpException();
            }
            return $this->render('tests-list', ['level' => $level, 'tests'=>$level->tests]);
        }

        public function actionTest($level, $test) {
            $level = TacticsLevel::findBySlug($level);
            if( $level == null ) {
                throw new NotFoundHttpException();
            }
            $test = TacticsTest::findOne($test);
            if( $test == null ) {
                throw new NotFoundHttpException();
            }

            return $this->render('test', ['test'=>$test, 'level'=>$level]);
        }


    }
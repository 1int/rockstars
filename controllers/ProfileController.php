<?php
    /**
     * Crafted by Pavel Lint 01/05/2018
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\Member;

    use app\models\TacticsLevel;
    use app\models\TacticsTest;

    use yii\web\Controller;
    use yii\web\NotFoundHttpException;
    use Yii;

    class ProfileController extends Controller {

        public function actionIndex($uname) {
            $member = Member::findByUsername($uname);
            if( !$member ) {
                throw new NotFoundHttpException("User not found");
            }


            $tests = TacticsLevel::findOne(1)->publishedTests;

            $scores = [];
            $labels = [];
            foreach($tests as $test) {
                $labels[] = 'Test ' . $test->id;
                $scores[] = $test->scoreFor($member->id);
            }

            return $this->render('index', ['member'=>$member, 'labels'=>$labels, 'scores'=>$scores]);
        }

    }
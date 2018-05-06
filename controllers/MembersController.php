<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\Member;
    use Yii;
    use yii\web\Controller;
    use yii\web\NotFoundHttpException;
    use app\models\TacticsLevel;

    class MembersController extends Controller
    {
        /**
         * Displays homepage.
         *
         * @return string
         */
        public function actionIndex()
        {
            $members = Member::find()->where('show_on_homepage=1')->all();
            shuffle($members);
            return $this->render('index', ['members'=>$members]);
        }

        public function actionProfile($uname) {
            $member = Member::findByUsername($uname);
            if( !$member ) {
                throw new NotFoundHttpException("User not found");
            }


            $tests = TacticsLevel::findOne(1)->publishedTests;

            $scores = [];
            $labels = [];
            foreach($tests as $test) {
                $labels[] = 'Test ' . $test->id;
                $scores[] =  $test->scoreFor($member->id);
            }

            return $this->render('profile', ['member'=>$member, 'labels'=>$labels, 'scores'=>$scores]);
        }
    }
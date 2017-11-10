<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\Member;
    use Yii;
    use yii\web\Controller;

    class MembersController extends Controller
    {
        /**
         * Displays homepage.
         *
         * @return string
         */
        public function actionIndex()
        {
            $members = Member::find()->all();
            shuffle($members);
            return $this->render('members-view', ['members'=>$members]);
        }
    }
<?php
    /**
     * Crafted by Pavel Lint 15/05/2018
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;
    use app\models\Member;
    use Yii;
    use yii\web\Controller;
    use yii\base\Action;

    class BaseController extends Controller {

        /**
         * @param Action $action the action to be executed.
         * @return bool
         */
        public function beforeAction($action) {
            if( parent::beforeAction($action) ) {
                $member = Yii::$app->user->identity;
                /**
                 * @var Member|null $member
                 */

                if( $member ) {
                    date_default_timezone_set('Europe/Moscow');
                    $member->lastseen = date('Y-m-d H:i:s', time());
                    $member->save();
                }
                return true;
            }
            else {
                return false;
            }
        }
    }

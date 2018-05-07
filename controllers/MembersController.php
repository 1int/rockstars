<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\Member;
    use Yii;
    use yii\web\Controller;
    use yii\web\HttpException;
    use yii\web\NotFoundHttpException;
    use app\models\TacticsLevel;
    use yii\web\UploadedFile;

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


            if( Yii::$app->request->isPost ) {
                if( Yii::$app->user->isGuest || Yii::$app->user->identity->getId() != $member->id ) {
                    throw new HttpException(403, "Can't post this");
                }

                $avatar = UploadedFile::getInstanceByName('avatar');
                if( $avatar != null ) {
                    $info = getimagesize($avatar->tempName);
                    if( $info == false ) {
                        throw new HttpException(400, 'Please select a jpeg or png file');
                    }
                    if (($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                        throw new HttpException(400, 'Please select a jpeg or png file');
                    }

                    $path = '/images/avatars/' . $avatar->name;
                    if( !$avatar->saveAs(Yii::getAlias('@webroot') . $path) ) {
                        throw new HttpException(400, 'Unable to save file');
                    }
                    $member->avatar = Yii::getAlias('@web') . $path;
                    $member->save();
                }

                $bio = Yii::$app->request->post('description');
                if( $bio ) {
                    $member->bio = strip_tags($bio, '<br><b><s><u><i>');
                    $member->save();
                }

                return "ok";
            }

            return $this->render('profile', ['member'=>$member, 'labels'=>$labels, 'scores'=>$scores]);
        }
    }
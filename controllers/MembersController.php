<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use app\models\Member;
    use Yii;
    use yii\web\HttpException;
    use yii\web\NotFoundHttpException;
    use app\models\TacticsLevel;
    use yii\web\UploadedFile;
    use app\models\NotableGame;
    use app\models\Event;

    class MembersController extends BaseController
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
                    $member->clearThumbs();
                }

                $bio = Yii::$app->request->post('description');
                if( $bio ) {
                    $member->bio = strip_tags($bio, '<br><b><s><u><i>');
                }
                $email = Yii::$app->request->post('private-email');
                if( $email ) {
                    $member->email = $email;
                    if( Yii::$app->request->post('private-sms') ) {
                        $member->receive_sms = true;
                    }
                    else {
                        $member->receive_sms = false;
                    }
                }

                $phone = Yii::$app->request->post('private-phone');
                if( $phone ) {
                    $member->phone = $phone;
                }
                $pass = Yii::$app->request->post('pass');
                if( $pass ) {
                    $repeat = Yii::$app->request->post('repeat');
                    $pass = trim($pass);
                    $repeat = trim($repeat);
                    if( $pass != $repeat ) {
                        throw new HttpException(400, 'Password do not match');
                    }
                    if( strlen($pass) < 3 ) {
                        throw new HttpException(400, 'Password is too short');
                    }
                    $member->password = Member::hashPassword($pass);
                }
                $member->save();

                $gameurl = Yii::$app->request->post('gameurl');
                if( $gameurl ) {
                    $matches = [];
                    preg_match('/https:\/\/lichess.org\/([a-zA-Z0-9]{7,20})/', $gameurl, $matches );
                    if( isset($matches[1]) ) {
                        $game = new NotableGame();
                        $game->player_id = $member->id;
                        $game->lichess_id = substr($matches[1], 0, 8);
                        $game->description = strip_tags(Yii::$app->request->post('gamedesc'));
                        if( !$game->save() ) {
                            throw new HttpException(400, 'Failed to save game');
                        }
                        return $game->id;
                    }
                    else {
                        throw new HttpException(400, 'Invalid lichess game url');
                    }
                }

                if( Yii::$app->request->isAjax ) {
                    return "ok";
                }
            }

            if( Yii::$app->request->isDelete ) {
                if( Yii::$app->user->isGuest || Yii::$app->user->identity->getId() != $member->id ) {
                    throw new HttpException(403, "Can't delete this");
                }
                $id = Yii::$app->request->bodyParams['gid'];
                $game = NotableGame::findOne($id);
                $game->delete();
                return "ok";
            }

            $events = Event::GetUpcomingEventsList($member->id, 4);
            return $this->render('profile', ['member'=>$member, 'labels'=>$labels, 'scores'=>$scores, 'events'=>$events]);
        }
    }
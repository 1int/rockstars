<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
use \yii\web\IdentityInterface;
use \app\classes\lichess\Player;
use yii\helpers\Json;
use yii\image\drivers\Image;


/**
 * This is the model class for table "members".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $avatar
 * @property string $bio
 * @property int $role
 * @property int $show_on_graph
 * @property int $show_on_homepage
 * @property int $show_in_pairings
 * @property string $link
 * @property string $lichessLink
 * @property string $usernameWithLink
 * @property string $nameWithLink
 * @property string $password
 * @property int $rating_blitz
 * @property int $rating_bullet
 * @property int $rating_rapid
 * @property string $lichess_profile
 * @property string $avatarUrl
 * @property string $email
 * @property string $phone
 * @property string $firstName
 * @property string $lastseen
 * @property string $regdate
 * @property int $rockstarsRating
 * @property string $smallAvatar
 * @property string $mediumAvatar
 * @property bool $receive_sms
 *
 * @property NotableGame[] $notableGames
 * @property Event[] $events
 * @property TacticsTestResult[] $testResults
 */
class Member extends ActiveRecord implements IdentityInterface
{
    const USER = 0;
    const ADMIN = 1;
    const MANAGER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'members';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'username', 'role'], 'required'],
            [['bio'], 'safe'],
            [['role', 'show_on_graph'], 'integer'],
            [['name', 'avatar'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 127],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'avatar' => 'Avatar',
            'bio' => 'Bio',
            'role' => 'Role',
            'plays_on_lichess' => 'Plays On Lichess',
        ];
    }

    /**
     * @return string
     */
    public function getLink() {
        return sprintf("/@/%s", $this->username);
    }

    /**
     * @return string
     */
    public function getLichessLink() {
        return sprintf("https://lichess.org/@/%s", $this->username);
    }

    /**
     * @return string
     */
    public function getUsernameWithLink() {
        return sprintf('<a href="/@/%s" target="_blank">%s</a>', $this->username, $this->username);
    }

    /**
     * @return string
     */
    public function getNameWithLink() {
        return sprintf('<a href="/@/%s" target="_blank">%s</a>', $this->username, $this->name);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function validateAuthKey($key) {
        return false;
    }

    public function validatePassword($p) {
        return $this->password == Member::hashPassword($p);
    }

    /**
     * @param int|string $id
     * @return Member|null
     */
    public static function findIdentity($id) {
        return Member::findOne($id);
    }

    /**
     * @param mixed $token
     * @return Member|null
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;
    }

    /**
     * @return string
     */
    public function getAuthKey() {
        return "";
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @param $name
     * @return Member|null
     */
    static function findByUsername($name) {
        return Member::find()->where('username = :username', ['username' => $name])->one();
    }

    /**
     * @param TacticsTest $test
     * @return bool
     */
    function hasFinishedTest($test) {
        /**
         * @var TacticsTestResult $result
         */
        $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:player_id',
            ['test_id'=>$test->id, 'player_id'=>$this->id])->one();
        if( $result == null ) {
            return false;
        }
        if( $result->finish )
            return true;

        if( $result->timePassed() ) {
            $test->finish($this->id);
            return true;
        }
    }

    function canManageTourneys() {
        return $this->role == self::ADMIN || $this->role = self::MANAGER;
    }

    function canInputAnswers() {
        return $this->role == self::ADMIN || $this->username == 'marisha';
    }

    function canSendSms() {
        return $this->role == self::ADMIN; //allow someone else to send team sms maybe?
    }

    function canInputOptions()
    {
        return $this->role == self::ADMIN || $this->role == self::MANAGER;
    }

    /**
     * @param Player $p
     */
    public function updateLichessData($p) {
        $this->rating_blitz = intval($p->getBlitzRating());
        $this->rating_bullet = intval($p->getBulletRating());
        $this->rating_rapid = intval($p->getRapidRating());
        $this->lichess_profile = JSON::encode($p);
        $this->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotableGames() {
        return $this->hasMany(NotableGame::className(), ['player_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents() {
        return $this->hasMany(Event::className(), ['master_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestResults() {
        return $this->hasMany(TacticsTestResult::className(), ['player_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getAvatarUrl() {
        return $this->avatar ? $this->avatar : '/images/default-avatar.jpeg';
    }

    public function getFirstName() {
        $arr = explode(' ', $this->name);
        return $arr[0];
    }

    public function hasLoggedInPastTwoWeeks() {
        $timestamp = strtotime($this->lastseen);
        return time() - $timestamp <= (3600*24*7*2);
    }

    public function hasRegisteredLongAgo() {
        $timestamp = strtotime($this->regdate);
        return time() - $timestamp >= (3600 * 24 * 180);
    }

    public static function hashPassword($p) {
        return md5( 'rockstar'. $p );
    }

    public function getRockstarsRating() {
        //заполненность, ведет ли лекции, заходил на сайт, полгода с нами, участие в командных битвах, прошел все тесты, ссылка на сайт
        $ret = 0;

        //1. One star if added a notable game
        if( count($this->notableGames) > 0 ) {
            $ret++;
        }

        //2. Has events
        if( count($this->events) > 0 ) {
            $ret++;
        }

        //3. Registered more than half a year ago
        if( $this->hasRegisteredLongAgo() ) {
            $ret++;
        }

        //4. Played in a team match
        $sql = "SELECT COUNT(*) FROM tourney_match WHERE white=:name OR black=:name";
        if( Yii::$app->db->createCommand($sql, ['name'=>$this->username])->queryScalar() > 0 ) {
            $ret++;
        }

        //5. Прошел все тесты
        if( count($this->testResults) == TacticsTest::find()->where('published=1')->count() ) {
            $ret++;
        }

        //6. Ссылка на сайт
        if( $this->lichess_profile && strpos($this->lichess_profile, 'rockstarschess.com') !== false ) {
            $ret++;
        }

        return max(min($ret, 5), 1);
    }

    /**
     * @return string
     */
    public function getMediumAvatar() {
        return $this->getAvatarOfSize(180);
    }

    /**
     * @return string
     */
    public function getSmallAvatar() {
        return $this->getAvatarOfSize(35);
    }

    /**
     * @param $size
     * @return string
     * @throws \yii\base\ErrorException
     */
    protected function getAvatarOfSize($size) {

        $dir = Yii::getAlias('@webroot') . '/images/avatars/' . $size;
        if( !is_dir($dir) ) {
            mkdir($dir);
        }

        if( !$this->avatar ) {
            $path =  $dir . '/default.jpeg';
            if( !file_exists($path) ) {
                $p = Yii::getAlias('@webroot') . '/images/default-avatar.jpeg';
                $img = Yii::$app->image->load($p);
                $img->resize($size, $size, Image::CROP)->save($path);
            }
            return '/images/avatars/' . $size . '/default.jpeg';
        }

        $path = Yii::getAlias('@webroot') . '/images/avatars/'.$size.'/' . $this->id . '.jpeg';
        if( !file_exists($path) ) {
            /** @var Image $img */
            $p = Yii::getAlias('@webroot') . '/' . $this->avatar;
            $img = Yii::$app->image->load($p);
            $img->resize($size, $size, Image::CROP)->save($path);
        }
        return '/images/avatars/'. $size .'/' . $this->id . '.jpeg';
    }

    public function clearThumbs() {
        $medium = Yii::getAlias('@webroot') . '/images/avatars/180/' . $this->id . '.jpeg';
        $small = Yii::getAlias('@webroot') . '/images/avatars/35/' . $this->id . '.jpeg';
        if( file_exists($medium) ) {
            unlink($medium);
        }
        if( file_exists($small) ) {
            unlink($small);
        }
    }

}

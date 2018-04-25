<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "members".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $avatar
 * @property string $bio
 * @property int $role
 * @property int $show_in_graph
 * @property string $link
 * @property string $usernameWithLink
 * @property string $nameWithLink
 * @property string $password
 */
class Member extends ActiveRecord implements \yii\web\IdentityInterface
{
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
            [['name', 'username', 'bio', 'role'], 'required'],
            [['bio'], 'string'],
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
        return sprintf("http://lichess.org/@/%s", $this->username);
    }

    /**
     * @return string
     */
    public function getUsernameWithLink() {
        return sprintf('<a href="http://lichess.org/@/%s" target="_blank">%s</a>', $this->username, $this->username);
    }

    /**
     * @return string
     */
    public function getNameWithLink() {
        return sprintf('<a href="http://lichess.org/@/%s" target="_blank">%s</a>', $this->username, $this->name);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function validateAuthKey($key) {
        return false;
    }

    public function validatePassword($p) {
        return $this->password == md5('rockstar'.$p);
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
            $test->finish();
            return true;
        }
    }

}

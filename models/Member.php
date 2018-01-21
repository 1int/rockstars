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
 */
class Member extends ActiveRecord
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

}

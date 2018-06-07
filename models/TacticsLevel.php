<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "tactics_levels".
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property int $time
 * @property string $description
 * @property int $total_tests
 * @property int $positions_in_test
 * @property int $start_position
 *
 * @property TacticsTest[] $tests
 * @property TacticsTest[] $publishedTests
 */
class TacticsLevel extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tactics_levels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'time'], 'required'],
            [['time'], 'integer'],
            [['name', 'slug', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTests()
    {
        return $this->hasMany(TacticsTest::className(), ['level_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPublishedTests() {
        return $this->hasMany(TacticsTest::className(), ['level_id' => 'id'])->where('published = 1');
    }

    /**
     * @param string $slug
     * @return TacticsLevel|null
     */
    static public function findBySlug($slug) {
        return TacticsLevel::find()->where('slug=:slug', ['slug'=>$slug])->one();
    }
}

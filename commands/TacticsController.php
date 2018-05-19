<?php
    /**
     * Crafted by Pavel Lint 30/04/2018
     * Mail to: pavel@1int.org
     */

    namespace app\commands;
    use yii\console\Controller;
    use app\models\TacticsTest;
    use Yii;
    use yii\image\drivers\Image;

    class TacticsController extends Controller {

        public function actionPublish() {
            /** @var TacticsTest $test */
            $test = TacticsTest::find()->where('published = 0')->orderBy('id asc')->one();
            $test->published = 1;
            $test->save();
        }

        public function actionResizeImages() {
            $dir = Yii::getAlias('@app') . '/assets/tactics';
            for($test = 1; $test <=50; $test++) {
                for($pos = 1; $pos <= 12; $pos++) {
                    $image = Yii::$app->image->load($dir . '/' . "test{$test}_{$pos}.jpeg");
                    $image->resize(300, 300, Image::WIDTH)->save();
                }
            }
        }
    }

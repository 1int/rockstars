<?php
    /**
     * Crafted by Pavel Lint 30/04/2018
     * Mail to: pavel@1int.org
     */

    namespace app\commands;
    use yii\console\Controller;
    use app\models\TacticsTest;

    class TacticsController extends Controller {

        public function actionPublish() {
            /** @var TacticsTest $test */
            $test = TacticsTest::find()->where('published = 0')->orderBy('id asc')->one();
            $test->published = 1;
            $test->save();
        }
    }

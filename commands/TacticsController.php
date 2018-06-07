<?php
    /**
     * Crafted by Pavel Lint 30/04/2018
     * Mail to: pavel@1int.org
     */

    namespace app\commands;
    use app\models\TacticsLevel;
    use app\models\TacticsPosition;
    use Yii;
    use yii\console\Controller;

    use \Imagick;
    use yii\image\drivers\Image;
    use yii\base\Action;

    use app\models\TacticsTest;
    use app\classes\stockfish\Stockfish;

    class TacticsController extends Controller {

        var $split = 5;
        var $level = 1;
        var $home = '';

        /**
         * @param Action $action
         * @return bool
         */
        public function beforeAction($action) {
            if( parent::beforeAction($action) ) {
                $this->home = Yii::getAlias('@app') . '/assets/tactics_orig/level' . $this->level;
                return true;
            }
            return false;
        }

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

        public function actionCropBoard() {
            $dir = $this->home;
            for($test = 1; $test <=50; $test++) {
                for($pos = 1; $pos <= 12; $pos++) {
                    print( "Image {$test}_{$pos}\n");
                    $img = new \Imagick($dir . '/' . "test{$test}_{$pos}.jpeg");
                    $w = $img->getImageWidth();
                    $h = $img->getImageHeight();



                    $x = $w/2;
                    $y = 0;
                    $avg = 100;
                    $prev = 100;
                    $top = 0;
                    $bottom = 0;
                    $left = 0;

                    do {
                        $prev = $avg;
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                        if( $avg < 20 && $prev < 20 ) {
                            $top = $y - 1;
                            break;
                        }
                        $y++;
                    }
                    while(true);


                    $x = 0;
                    $y = $h / 2;
                    $avg = 100;
                    $prev = 100;

                    do {
                        $prev = $avg;
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                        if( $avg < 20 && $prev < 20 ) {
                            $left = $x - 1;
                            break;
                        }
                        $x++;
                    }
                    while(true);

                    $x = $w / 2;
                    $y = $h;
                    $avg = 100;
                    $prev = 100;

                    do {
                        $prev = $avg;
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                        if( $avg < 20 && $prev < 20 ) {
                            $bottom = $y + 1;
                            break;
                        }
                        $y--;
                    }
                    while(true);


                    $newSize = $bottom - $top + 1;
                    $img->cropImage($newSize, $newSize, $left, $top);
                    $img->writeImage($dir . "/test{$test}_{$pos}.board.jpeg");
                }
            }
        }


        public function actionSplitBoard() {
            $dir = Yii::getAlias('@app') . '/assets/tactics_orig';
            for($test = 1; $test <=50; $test++) {
                for ($pos = 1; $pos <= 12; $pos++) {
                    print("Image {$test}_{$pos}\n");
                    $img = new \Imagick($dir . '/' . "test{$test}_{$pos}.board.jpeg");
                    $w = $img->getImageWidth();
                    $h = $img->getImageHeight();

                    $d = $w / 16;
                    $offset_left = 0;
                    $offset_top = 0;
                    $offset_right = 0;
                    $offset_bottom = 0;


                    $x = 0;
                    $y = $d;
                    $count = 0;
                    $hadBorder = false;

                   // print( "left\n" );
                    do {
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                        if( $avg < 20 ) {
                            $hadBorder = true;
                        }
                        if( $avg >= 200 && $hadBorder ) {
                            $count++;
                        }

                        if( $count >= 8 ) {
                            $left = $x - 8;
                            break;
                        }
                        $x++;
                    }
                    while(true);



                    $x = $d;
                    $y = 0;
                    $count = 0;
                    $hadBorder = false;

                 //   print( "top\n" );
                    do {
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                        if( $avg < 20 ) {
                            $hadBorder = true;
                        }
                        if( $avg >= 200 && $hadBorder ) {
                            $count++;
                        }

                        if( $count >= 8 ) {
                            $top = $y - 8;
                            break;
                        }
                        $y++;
                    }
                    while(true);


                    $x = $w;
                    $y = $h - $d;
                    $count = 0;
                    $hadBorder = false;

                   // print( "right\n" );
                    do {
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                       // print $avg . "\n";
                        if( $avg < 20 ) {
                            $hadBorder = true;
                        }
                        if( $avg >= 200 && $hadBorder ) {
                            $count++;
                        }

                        if( $count >= 8 ) {
                            $right = $x + 8;
                            break;
                        }
                        $x--;
                    }
                    while(true);


                    $x = $w - $d;
                    $y = $h;
                    $count = 0;
                    $hadBorder = false;

                   // print( "bottom\n" );
                    do {
                        $p = $img->getImagePixelColor($x, $y)->getColor();
                        $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                        if( $avg < 20 ) {
                            $hadBorder = true;
                        }
                        if( $avg >= 200 && $hadBorder ) {
                            $count++;
                        }

                        if( $count >= 8 ) {
                            $bottom = $y + 8;
                            break;
                        }
                        $y--;
                    }
                    while(true);


                    $img->cropImage($right-$left+1, $bottom-$top+1, $left, $top);
                    $img->writeImage($dir . '/' . "test{$test}_{$pos}.board2.jpeg");
                    $img = null;

                    $img = new \Imagick($dir . '/' . "test{$test}_{$pos}.board2.jpeg");

                    $w = $img->getImageWidth() / 8;
                    $h = $img->getImageHeight() / 8;


                    if( !is_dir($dir . "/test{$test}_{$pos}")) {
                        mkdir($dir . "/test{$test}_{$pos}");
                    }
                    for ($i = 0; $i < 8; $i++) {
                        for ($j = 0; $j < 8; $j++) {
                            $c = clone $img;
                            $c->cropImage($w, $h, $i * $w, $j * $h);
                            $c->writeImage($dir . "/test{$test}_{$pos}/{$i}.{$j}.jpeg");
                            $c = null;
                        }
                    }

                }
            }
        }

        /**
         * Get the average pixel value in regions
         * @param Imagick $img
         * @return array 1-dimensional array of average region pixel values
         */
        protected function getPixelMap($img) {

            $w = $img->getImageWidth() / $this->split;
            $h = $img->getImageHeight() / $this->split;

            $ret = [];

            for( $x_region = 0; $x_region < $this->split; $x_region++ ) {
                for( $y_region = 0; $y_region < $this->split; $y_region++ ) {
                    $avg = 0;
                    $count = 0;
                    for( $x = $w * $x_region; $x <  $w*($x_region+1); $x++ ) {
                        for( $y = $h * $y_region; $y < $h*($y_region+1); $y++ ) {
                            $pixel = $img->getImagePixelColor($x, $y)->getColor();
                            $avg +=  ($pixel['r'] + $pixel['g'] + $pixel['b']) / 3;
                            $count++;
                        }
                    }
                    if( $count == 0 ) {
                        $ret[$x_region * $this->split + $y_region] = -1;
                    }
                    else {
                        $ret[$x_region * $this->split + $y_region] = $avg / $count;
                    }

                }
            }

            $sum = array_sum($ret);
            foreach($ret as &$val) {
                $val /= $sum;
                $val *= 1000;
            }

            return $ret;
        }


        /**
         * @param Imagick $img
         * @return float
         */
        protected function getAvgPixel($img) {
            $w = $img->getImageWidth();
            $h = $img->getImageHeight();
            $avg = 0;
            for( $x = 0; $x < $w; $x++ ) {
                for( $y = 0; $y < $h; $y++ ) {
                    $pixel = $img->getImagePixelColor($x, $y)->getColor();
                    $avg +=  ($pixel['r'] + $pixel['g'] + $pixel['b']) / 3;
                }
            }

            return $avg / ($w * $h);
        }

        /**
         * @param array $map1
         * @param array $map2
         * @return float
         */
        protected function pixelMapDiff($map1, $map2) {
            $ret = 0;
            for($i = 0; $i < count($map1); $i++) {
                $ret += abs($map1[$i] - $map2[$i]);
            }
            return $ret / count($map1);
        }


        /**
         * @param array $models
         */
        protected function loadModels( &$models ) {

            $models['white'] = [];
            $models['black'] = [];

            foreach($models as $color => $unused) {
                $dir = new \DirectoryIterator($this->home . '/model/' . $color);
                foreach ($dir as $file) {
                    if (!$file->isDot() && $file->getFilename() != '.DS_Store') {
                        $isBlack = $file->getExtension() == 'jpg';
                        $name = $file->getBasename();
                        $name = str_replace('.jpeg', '', $name);
                        $name = str_replace('.jpg', '', $name);
                        if (!$isBlack) {
                            $name = strtoupper($name);
                        }
                        $path = $dir->getPath() . '/' . $file->getFilename();
                        $models[$color][$name] = [ ];
                        $models[$color][$name]['path'] = $path;
                        $img = new \Imagick($path);
                        //  $img->cropImage(90, 90, 20, 20);
                        $models[$color][$name]['img'] = $img;
                        $models[$color][$name]['map'] = $this->getPixelMap($models[$color][$name]['img']);
                    }
                }
            }
        }

        public function actionFen($testNumber, $positionNumber) {
            $models = [];
            $this->loadModels($models);

            $dir = $this->home . '/data/test' . $testNumber . '_' . $positionNumber;
            $ret = '';
            for( $y = 0; $y < 8; $y++ ) {
                for( $x = 0; $x < 8; $x++) {
                    $path = $dir . '/' . $x . '.' . $y . '.jpeg';
                    $isBlack = ($x + $y) % 2 == 1;
                    $src = $isBlack ? $models['black'] : $models['white'];

                    $img = new Imagick($path);
                //    $img->cropImage(90, 90, 20, 20);

                    $map = $this->getPixelMap($img);
                    $minDiff = -1;
                    $recognizedName = '';

                    foreach($src as $name => $value) {
                        $diff = $this->pixelMapDiff($value['map'], $map);
                        if( $diff < $minDiff || $minDiff == -1 ) {
                            $recognizedName = $name;
                            $minDiff = $diff;
                        }
                    }
                    $ret .= $recognizedName;
                }
                if( $y != 7 ) {
                    $ret .= '/';
                }
            }

            $fen = $this->fixQueens($ret, $testNumber, $positionNumber);
            if( !defined('NO_PRINT') ) {
                print "FEN: " . $fen . "\n";
            }
            return $fen;
        }

        /**
         * @param Imagick $img
         * @return float
         */
        protected function getLongestVerticalBlackLine($img) {
            $w = $img->getImageWidth();
            $h = $img->getImageHeight();
            $longest = 0;

            for( $x = $w/2 - 15; $x < $w/2 + 15; $x++ ) {
                $current = 0;
                for( $y = 0; $y < $h; $y++ ) {
                    $pixel = $img->getImagePixelColor($x, $y)->getColor();
                    $avg = ($pixel['r'] + $pixel['g'] + $pixel['b']) / 3;
                    if( $avg < 20 ) {
                        $current++;
                    }
                    else {
                        if( $current > $longest ) {
                            $longest = $current;
                        }
                        $current = 0;
                    }
                }
                if( $current > $longest ) {
                    $longest = $current;
                }
            }

            return $longest;
        }

        /**
         * Queens are very similar, so we use a different approach to tell them apart:
         * the max continuous black line lenght in the center.
         *
         * @param string $fen
         * @param int $testNumber
         * @return string
         */
        protected function fixQueens($fen, $testNumber, $positionNumber) {
            $dir = $this->home . '/data/test' . $testNumber . '_' . $positionNumber . '/';
            $index = -1;

            $model1 = new Imagick($this->home . '/model/white/q.jpeg');
            //$model1->cropImage(90, 90, 20, 20);
            $whiteMaxLine = $this->getLongestVerticalBlackLine($model1);
            $model2 = new Imagick($this->home . '/model/black/q.jpg');
           // $model2->cropImage(90, 90, 20, 20);
            $blackMaxLine = $this->getLongestVerticalBlackLine($model2);


            if( !defined('NO_PRINT') ) {
                print "black line: " . $blackMaxLine . "\n";
                print "white line: " . $whiteMaxLine . "\n";
            }

            while (($index = stripos($fen, 'q', $index + 1)) !== false) {
                $path = $dir . (($index % 9) . '.' . intval($index / 9)) . '.jpeg';
                $img = new Imagick($path);
                //$img->cropImage(90, 90, 20, 20);
                $line = $this->getLongestVerticalBlackLine($img);
                if( !defined('NO_PRINT') ) {
                    print "this line: " . $line . "\n";
                }
                if (abs($line - $blackMaxLine) > abs($line - $whiteMaxLine)) {
                    $fen[$index] = 'Q';
                }
                else {
                    $fen[$index] = 'q';
                }
            }
            return $fen;
        }

        /**
         * @param int $testNumber
         * @param int $positionNumber
         * @return null|string
         */
        public function actionTestStockfish($testNumber, $positionNumber) {
            $fen = $this->actionFen($testNumber, $positionNumber);
            $stockfish = new Stockfish();
            return $stockfish->bestMoveFromFen($fen, true);
        }


        /**
         * @param Imagick $img
         * @return bool
         */
        protected function isBlackToMove($img) {

            $w = $img->getImageWidth();
            $h = $img->getImageHeight();
            $left = 0;
            $bottom = 0;

            $x = 0;
            $y = $h / 2;
            $count = 0;
            $hadBorder = false;

            // find left border
            do {
                $p = $img->getImagePixelColor($x, $y)->getColor();
                $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                if( $avg < 20 ) {
                    $hadBorder = true;
                }
                if( $avg >= 200 && $hadBorder ) {
                    $count++;
                }

                if( $count >= 8 ) {
                    $left = $x - 8;
                    break;
                }
                $x++;
            }
            while(true);

            $x = $w / 2;
            $y = $h;
            $avg = 100;

            // find bottom border
            do {
                $prev = $avg;
                $p = $img->getImagePixelColor($x, $y)->getColor();
                $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                if( $avg < 20 && $prev < 20 ) {
                    $bottom = $y + 1;
                    break;
                }
                $y--;
            }
            while(true);

            // find the lowest black pixel (the bottom of "1.")
            for( $x = $left; $x < $left + 50; $x++ ) {
                for ($y = $bottom; $y < $h; $y++) {
                    $p = $img->getImagePixelColor($x, $y)->getColor();
                    $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                    if ($avg < 20 && $prev < 20) {
                        $bottom = $y;
                    }
                }
            }

            //Now, count the black/white change count horizontally
            $colorChangeCount = 0;
            for( $y = $bottom; $y > $bottom - 25; $y--) {
                $prev = 0;
                $count = 0;
                for( $x = $left; $x < $w; $x++ ) {
                    $p = $img->getImagePixelColor($x, $bottom)->getColor();
                    $avg = ($p['r'] + $p['g'] + $p['b']) / 3;
                    if( $avg < 20 && $prev > 100 ) {
                        $bottom = $y;
                        $count++;
                        if( $count > $colorChangeCount ) {
                            $colorChangeCount = $count;
                        }
                    }
                    $prev = $avg;
                }
            }

            // If we have "..." then black to white change count will be 5 or more
            if( !defined('NO_PRINT') ) {
                print "color change count: " . $colorChangeCount . "\n";
            }
            return $colorChangeCount > 4;
        }

        public function actionBlackMove($testNumber, $positionNumber) {
            $path = $this->home . '/img/test' . $testNumber . '_' . $positionNumber . '.jpeg';
            $img = new Imagick($path);
            $isBlack = $this->isBlackToMove($img);

            if( !defined('NO_PRINT') ) {
                print "Black: " . ($isBlack ? "true" : "false");
                print "\n";
            }
            return $isBlack;
        }

        /**
         * @param int $testNumber
         * @param int $positionNumber
         * @return bool
         */
        public function actionRecognizeOne($testNumber, $positionNumber) {

            if( php_sapi_name() != "cli") {
                define('NO_PRINT', true);
            }

            //1. Let's understand if black is to move first
            $isBlack = $this->actionBlackMove($testNumber, $positionNumber);

            //2. Recognize the image
            $fen = $this->actionFen($testNumber, $positionNumber);

            //3. Get stockfish best move
            $stockfish = new Stockfish();
            $bestMove = $stockfish->bestMoveFromFen($fen, $isBlack);

            //4. Save the data
            $positionId = ($this->level - 1) * 600 + ($testNumber-1)*12 + $positionNumber;
            $testId = ($this->level - 1) * 12 + $testNumber;

            /**
             * @var TacticsPosition $model
             */
            $model = TacticsPosition::findOne($positionId);
            if( $model == null ) {
                $model = new TacticsPosition();
                $model->id = $positionId;
                $model->test_id = $testId;
                $model->verified = 0;
                $model->points = 0;
            }

            $model->dotdotdot = $isBlack ? 1 : 0;
            $model->stockfish_answer = $bestMove;
            $model->fen = $fen;
            if( !$model->verified ) {
                $model->answer = $stockfish->humanReadableMove($bestMove, $fen);
            }

            $ret = $model->save();
            return $ret && (!!$bestMove);
        }

        public function actionRecognize() {
            define( 'NO_PRINT', true );

            $level = TacticsLevel::findOne($this->level);
            for($testNumber = 1; $testNumber <=  $level->total_tests; $testNumber++) {
                for($position = 1; $position <= $level->positions_in_test; $position++) {
                    $posid = $level->start_position + ($testNumber-1) * $level->positions_in_test + $position - 1;
                    /** @var TacticsPosition $model */
                    $model = TacticsPosition::findOne($posid);

                    if( $model && $model->verified ) {
                        continue;
                    }

                    print "Recognizing Level 1, test{$testNumber}_{$position}...";
                    $ret = $this->actionRecognizeOne($testNumber, $position);
                    if( !$ret ) {
                        print "FAIL.\n";
                        die;
                    }
                    else {
                        print "OK.\n";
                    }
                }
            }
        }

    }

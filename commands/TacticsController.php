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
    use \Imagick;

    class TacticsController extends Controller {

        var $split = 4;
        var $level = 1;

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
            $dir = Yii::getAlias('@app') . '/assets/tactics_orig';
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

        public function actionFen($testNumber) {
            $home = Yii::getAlias('@app') . '/assets/tactics_orig';
            $models = [];
            $models['white'] = [];
            $models['black'] = [];

            $dir = new \DirectoryIterator($home . '/model/white');
            foreach ($dir as $file) {
                if (!$file->isDot()  && $file->getFilename() != '.DS_Store') {
                    $isBlack = $file->getExtension() == 'jpg';
                    $name = $file->getBasename();
                    $name = str_replace('.jpeg', '', $name);
                    $name = str_replace('.jpg', '', $name);
                    if( !$isBlack ) {
                        $name = strtoupper($name);
                    }
                    $path = $dir->getPath() . '/' . $file->getFilename();
                    $models['white'][$name] = [];
                    $models['white'][$name]['path'] = $path;
                    $img = new \Imagick($path);
                    $img->cropImage(90, 90, 20, 20);
                    $models['white'][$name]['img'] = $img;
                    $models['white'][$name]['map'] = $this->getPixelMap($models['white'][$name]['img']);
                }
            }

            $dir = new \DirectoryIterator($home . '/model/black');
            foreach( $dir as $file ) {
                if (!$file->isDot() && $file->getFilename() != '.DS_Store') {
                    $isBlack = $file->getExtension() == 'jpg';
                    $name = $file->getBasename();
                    $name = str_replace('.jpeg', '', $name);
                    $name = str_replace('.jpg', '', $name);
                    if( !$isBlack ) {
                        $name = strtoupper($name);
                    }
                    $path = $dir->getPath() . '/' . $file->getFilename();
                    $models['black'][$name] = [];
                    $models['black'][$name]['path'] = $path;
                    $img = new \Imagick($path);
                    $img->cropImage(90, 90, 20, 20);
                    $models['black'][$name]['img'] = $img;
                    $models['black'][$name]['map'] = $this->getPixelMap($models['white'][$name]['img']);
                }
            }


            $dir = $home . '/test1_' . $testNumber;
            $ret = '';
            for( $y = 0; $y < 8; $y++ ) {
                for( $x = 0; $x < 8; $x++) {
                    $path = $dir . '/' . $x . '.' . $y . '.jpeg';
                    $isBlack = ($x + $y) % 2 == 1;
                    $src = $isBlack ? $models['black'] : $models['white'];

                    $img = new Imagick($path);
                    $img->cropImage(90, 90, 20, 20);

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
                $ret .= '/';
            }



            $fen = $this->fixQueens($ret, $testNumber);
            print "FEN: " . $fen . "\n";
            return "FEN: " . $fen . "\n";
        }

        /**
         * @param Imagick $img
         * @return float
         */
        protected function getLongestVerticalBlackLine($img) {
            $w = $img->getImageWidth();
            $h = $img->getImageHeight();
            $longest = 0;

            for( $x = $w/2 - 5; $x < $w/2 + 5; $x++ ) {
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
        protected function fixQueens($fen, $testNumber) {
            $home = Yii::getAlias('@app') . '/assets/tactics_orig';
            $dir = $home . '/test' . $this->level . '_' . $testNumber . '/';
            $index = -1;

            $model1 = new Imagick($home . '/model/white/q.jpeg');
            $model1->cropImage(90, 90, 20, 20);
            $whiteMaxLine = $this->getLongestVerticalBlackLine($model1);
            $model2 = new Imagick($home . '/model/black/q.jpg');
            $model2->cropImage(90, 90, 20, 20);
            $blackMaxLine = $this->getLongestVerticalBlackLine($model2);

            while (($index = stripos($fen, 'q', $index + 1)) !== false) {
                $path = $dir . (($index % 9) . '.' . intval($index / 9)) . '.jpeg';
                $img = new Imagick($path);
                $img->cropImage(90, 90, 20, 20);
                $line = $this->getLongestVerticalBlackLine($img);
                if (abs($line - $blackMaxLine) > abs($line - $whiteMaxLine)) {
                    $fen[$index] = 'Q';
                }
                else {
                    $fen[$index] = 'q';
                }
            }
            return $fen;
        }
    }

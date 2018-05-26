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
    use app\classes\compareImages\compareImages;

    class TacticsController extends Controller {

        var $split = 4;

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

                   // die;
                }
            }
        }

        protected function avgPixel($img, $x, $y, $w, $h) {
            return 255;
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
                    $img->writeImage('white' . $name . ($isBlack? '.jpg':'.jpeg'));
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
                    $img->writeImage('black' . $name . ($isBlack? '.jpg':'.jpeg'));
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
                    $img->writeImage($x . '-' . $y . '.jpeg');

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

            print "FEN: " . $ret . "\n";
            return "FEN: " . $ret . "\n";
        }


        public function actionTest() {
            $class = new compareImages;
            print "Similarity empty -> knight: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test1.jpeg','/Users/Lint/Desktop/Test/n.jpeg');
            print "\nSimilarity empty -> empty: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test1.jpeg','/Users/Lint/Desktop/Test/1.jpeg');
            print "\nSimilarity empty -> knight #2: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test2.jpeg','/Users/Lint/Desktop/Test/n.jpeg');
            print "\nSimilarity empty -> empty #2: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test2.jpeg','/Users/Lint/Desktop/Test/1.jpeg');
            print "\nSimilarity empty -> knight #3: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test3.jpeg','/Users/Lint/Desktop/Test/n.jpeg');
            print "\nSimilarity empty -> empty #3: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test3.jpeg','/Users/Lint/Desktop/Test/1.jpeg');
            print "\nSimilarity empty -> knight #4: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test4.jpeg','/Users/Lint/Desktop/Test/n.jpeg');
            print "\nSimilarity empty -> empty #4: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test4.jpeg','/Users/Lint/Desktop/Test/1.jpeg');
            print "\nSimilarity knight -> knight #5: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test5.jpeg','/Users/Lint/Desktop/Test/n.jpeg');
            print "\nSimilarity knight -> empty #5: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test5.jpeg','/Users/Lint/Desktop/Test/1.jpeg');
            print "\nSimilarity knight -> knight #6: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test6.jpeg','/Users/Lint/Desktop/Test/n.jpeg');
            print "\nSimilarity knight -> empty #6: ";
            echo $class->compare('/Users/Lint/Desktop/Test/test6.jpeg','/Users/Lint/Desktop/Test/1.jpeg');
            print "\n";
        }

        public function actionTest2() {
            $img = new Imagick('/Users/Lint/Desktop/test/tes/test.jpeg');
            $data = $this->getPixelMap($img);
            print_r($data);

            $img = new Imagick('/Users/Lint/Desktop/test/tes/black1.jpeg');
            $data2 = $this->getPixelMap($img);
            print_r($data2);


            $img = new Imagick('/Users/Lint/Desktop/test/tes/blackN.jpeg');
            $data3 = $this->getPixelMap($img);
            print_r($data3);

            print "\n\n";
            print "Diff to empty: " . $this->pixelMapDiff($data, $data2) . "\n";
            print "Diff to knight: " . $this->pixelMapDiff($data, $data3) . "\n";
        }

        public function actionTest3() {
            for( $q = 1; $q <= 30; $q++) {
                $this->split = $q;
                print "Split: {$q}\n";
                $fen = $this->actionFen(5);
                if( strstr($fen, 'q') !== false ) {
                    print "Ok.\n";
                    print "Fen: " . $fen . "\n";
                    break;
                }
            }
        }
    }

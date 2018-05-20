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

    }

<?php
    /**
     * Crafted by Pavel Lint 20/03/2018
     * Mail to: pavel@1int.org
     */

    namespace app\commands;
    use yii\console\Controller;

    const IN_FOLDER = __DIR__ . '/../tests/img';
    const OUT_FOLDER = __DIR__ . '/../tests/out';

    class PositionSplitController extends Controller {

        public function actionIndex() {




            $images = scandir(IN_FOLDER, SCANDIR_SORT_NONE);
            uasort($images, function($a, $b) {
                $aPage = sscanf($a, "Page %d");
                $bPage = sscanf($b, "Page %d");
                return $aPage > $bPage;
            });


            $i = 0;
            $y_offset = 410;


            foreach($images as $image) {
                if ($image == '.' || $image == '..' || $image == '.DS_Store') {
                    continue;
                }
                $src = new \Imagick( IN_FOLDER . '/' . $image);

                $tile_width = $src->getImageWidth()/ 2;
                $tile_height = ($src->getImageHeight() - $y_offset - 270) / 3;

                for($h = 0; $h < 3; $h++) {
                    for($w = 0; $w < 2; $w++) {

                        $x = $w*$tile_width;
                        $y = $h*$tile_height + $y_offset;

                        $positionNumber = ($i % 2) * 6 + $h * 2 + $w + 1;

                        $image = clone $src;
                        $image->cropImage($tile_width, $tile_height, $x,$y);
                        $image->trimImage(20);
                        $image->writeImage( OUT_FOLDER . "/test" . (intval($i / 2) + 1) . '_' . $positionNumber . ".jpeg");

                    }
                }

                $i++;
            }


            print "Done.\n";



        }


    }
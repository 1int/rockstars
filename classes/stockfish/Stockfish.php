<?php
    /**
     * Crafted by Pavel Lint 02/06/2018
     * Mail to: pavel@1int.org
     */

    namespace app\classes\stockfish;


    class Stockfish {

        public $depth = 15;

        /**
         * @param string $fen
         * @param bool $isBlackToMove
         * @return null|string
         */
        public function bestMoveFromFen($fen, $isBlackToMove) {
            $shell = "#!/bin/sh\n" .
                "(\n".
                sprintf("echo \"position fen %s %s - -\";\n", $fen, $isBlackToMove ? 'b':'w') .
                "echo \"go depth ". $this->depth ."\";\n" .
                "sleep 1;\n" .
                ") | stockfish";
            $path = sys_get_temp_dir() . '/stockfish.sh';
            file_put_contents($path, $shell);
            chmod($path,  0777);
            $output = [];
            exec($path, $output);
            $ret = $output[count($output) - 1];

            $matches = [];
            $regexp = '/bestmove ([a-h][1-8])([a-h][1-8])/';
            if( preg_match($regexp, $ret, $matches) === 1 ) {
                if( !defined('NO_PRINT') ) {
                    print "best move - from " . $matches[1] . ' to ' . $matches[2] . "\n";
                }
                $ret = $matches[1] . '-' . $matches[2];
            }
            else {
                // invalid position probably
                if( !defined('NO_PRINT') ) {
                    print "Error!\n";
                    var_dump($ret);
                    print $shell;
                }
            }
            unlink($path);


            return $ret;
        }

        /**
         * @param string $move in form [c2-c4]
         * @param string $fen
         * @return string|null
         */
        public function humanReadableMove($move, $fen) {
            $matches = [];
            $fen = strtolower($fen);
            $regexp = '/([a-h])([1-8])-([a-h])([1-8])/';
            if( preg_match($regexp, $move, $matches) === 1 ) {
                $coord = [ ord(strtolower($matches[1])) - ord('a'), intval($matches[2])];
                $location = 9 * (8-$coord[1]) + $coord[0];
                $figure = $fen[$location];
                switch($figure) {
                    case 'b': return 'B' . strtolower($matches[3]) . $matches[4];
                    case 'n': return 'N' . strtolower($matches[3]) . $matches[4];
                    case 'k': return 'K' . strtolower($matches[3]) . $matches[4];
                    case 'q': return 'Q' . strtolower($matches[3]) . $matches[4];
                    case 'r': return 'R' . strtolower($matches[3]) . $matches[4];
                    case 'p': return strtolower($matches[3]) . $matches[4];
                }
            }
            return null;
        }

    }
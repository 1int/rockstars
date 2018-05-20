#!/bin/sh
( 
echo "position fen r1bq1r2/pp3pk1/5Npp/5n2/3Q4/3B2PP/PP4PK/2R2R2 w - -" ;
echo "go movetime 1000" ;
sleep 2
) | stockfish

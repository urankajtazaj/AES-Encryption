<?php
require 'src/AES.php';

$aes = new AES("Hello world", "123");

echo "<pre>";

$state = [
    [ '2B', '28','AB','09' ],
    [ '7E', 'AE','F7','CF' ],
    [ '15', 'D2','15','4F' ],
    [ '16', 'A6','88','3C' ],
];

echo "<h1>SUB BYTES TEST</h1>";
echo "<h3>SBOX</h3>";
$aes->printSbox();
echo "\n<hr>";
echo "<h3>Input</h3>";
for ($i = 0; $i < sizeof($state); $i++) {
    for ($j = 0; $j < sizeof($state[$i]); $j++) {
        echo $state[$i][$j] . "\t";
    }
    echo "\n";
}

echo "\n<hr>";
echo "<h3>Subbed input</h3>";
$aes->subBytes($state);

echo "</pre>";
<?php
//@ini_set("display_errors", 1);
require 'src/AES.php';
//@ini_set("display_errors", 1);

$aes = new AES("Hello world", "123");

//$numbers = array (
//    array(0xd4,0xe0,0xb8,0x1e),
//    array(0xbf,0xb4,0x41,0x27),
//    array(0x5d,0x52,0x11,0x98),
//    array(0x30,0xae,0xf1,0xe5)
//);
$state = [
    [ 'd4', 'e0','b8','1e' ],
    [ 'bf', 'b4','41','27' ],
    [ '5d', '52','11','98' ],
    [ '30', 'ae','f1','e5' ],
];
echo "<h3>State</h3>";
for ($i = 0; $i < sizeof($state); $i++) {
    for ($j = 0; $j < sizeof($state[$i]); $j++) {
        echo $state[$i][$j] . "\t";
    }
    echo "\n";
}
echo "\n<hr>";
$aes->mixColumns($state);




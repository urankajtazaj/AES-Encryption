<?php
//@ini_set("display_errors", 1);
require 'src/AES.php';
//@ini_set("display_errors", 1);

$aes = new AES("Hello world", "123");

//$state = array (
//    array(0xd4,0xe0,0xb8,0x1e),
//    array(0xbf,0xb4,0x41,0x27),
//    array(0x5d,0x52,0x11,0x98),
//    array(0x30,0xae,0xf1,0xe5)
//);

$state = array(
    array( "D9", "BF","FD","0D"),
    array( "2F", "D8","7B","37"),
    array( "5D", "9F","AB","0F"),
    array( "8A", "59","1C","80"));

echo "<h3>State</h3>";
for ($i = 0; $i < sizeof($state); $i++) {
    for ($j = 0; $j < sizeof($state[$i]); $j++) {
        echo $state[$i][$j] . "\t";
    }
    echo "\n";
}
echo "\n<hr>";
$state1 = $aes->mixColumns($state);
echo "Output";
echo "\n<hr>";
for ($i = 0; $i < sizeof($state1); $i++) {
    for ($j = 0; $j < sizeof($state1[$i]); $j++) {
        echo $state1[$i][$j] . "\t";
    }
    echo "\n";
}





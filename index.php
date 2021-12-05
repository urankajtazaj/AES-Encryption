<?php
require 'autoload.php';

$plaintext = 'Hello World     ';
$key = array(
    [ '54', '20', '61', '79' ],
    [ '68', '69', '20', '20' ],
    [ '69', '73', '6b', '66' ],
    [ '73', '20', '65', '6f' ],
);

$aes = new AES($key);

echo "<pre>";
echo "Cipher text\n---------------------\n" . $aes->encrypt($plaintext);
echo "</pre>";
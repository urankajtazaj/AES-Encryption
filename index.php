<?php
require 'autoload.php';

$plaintext = 'Hello World';
$key = array(
    [ '31', '35', '39', '33' ],
    [ '32', '36', '30', '34' ],
    [ '33', '37', '31', '35' ],
    [ '34', '38', '32', '36' ],
);

$aes = new AES($key);

echo "<pre>";
echo "Cipher text\n---------------------\n" . $aes->encrypt($plaintext);
echo "</pre>";
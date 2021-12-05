<?php
require 'autoload.php';

$plaintext = 'Hello World Hello World';
$key = '1234567890123456';

$aes = new AES($key);

echo "<pre>";
echo "BASE 64\n---------------------\n" . $aes->encrypt($plaintext)->getCipherText(true);
echo "\n\nHEX\n---------------------\n" . $aes->encrypt($plaintext)->getCipherText();
echo "</pre>";
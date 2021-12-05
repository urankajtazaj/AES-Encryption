<?php
require 'autoload.php';

$plaintext = 'Hello World Hello World';
$key = '1234567890123456';

$aes = new AES($key);

echo "<pre>";
echo "Base64 text\n---------------------\n" . $aes->encrypt($plaintext);
echo "</pre>";
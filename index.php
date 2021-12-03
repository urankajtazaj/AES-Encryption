<?php
require 'src/AES.php';

$aes = new AES("Hello world", "123");
echo $aes->getPlaintext();

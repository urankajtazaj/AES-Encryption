<?php
require 'src/AES.php';
//@ini_set("display_errors", 1);

$aes = new AES("Hello world", "123");

<?php
require '../autoload.php';

$plaintext = $_GET['plaintext'];
$key = $_GET['key'];

$aes = new AES($key);
$aesEncrypted = $aes->encrypt($plaintext);
?>

<hr>
<p><b>Base 16</b><br><?= $aesEncrypted->getCipherText() ?></p>
<p><b>Base 64</b><br><?= $aesEncrypted->getCipherText(true) ?></p>
<hr>

<?php 
require 'defuse-crypto.phar';

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;


function isWindows(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return true;
    } else {
        return false;
    }    
}


function readKey($filename) {
    if (isWindows()) {
        $fileMode = "rb";
    } else {
        $fileMode = "r";
    }

    $handle = fopen($filename, $fileMode);
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    return $contents;
}

function loadEncryptionKeyFromConfig($filename) {
    $keyAscii = readKey($filename);
    return Key::loadFromAsciiSafeString($keyAscii);
}

$filename = "defuse-crypto.key";
$key = loadEncryptionKeyFromConfig($filename);

$password = 'myp455w0rd';
$ciphertext = Crypto::encrypt($password, $key);

echo 'Password: ' . $password . "\n";
echo 'Ciphertext: ' . $ciphertext . "\n";

try {
    $secret_data = Crypto::decrypt($ciphertext, $key);
    echo 'Secret data: ' . $secret_data . "\n";
} catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
    echo 'wrong key' . "\n";
    echo 'ciphertext has changed since it was created' . "\n";
    echo 'ciphertext is corrupted' . "\n";
    echo 'ciphertext is intentionally modified' . "\n";
}
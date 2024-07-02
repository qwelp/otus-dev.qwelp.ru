<?php

$requireFiles = [
    '/vendor/autoload.php',
    '/local/php_interface/constants.php'
];

foreach ($requireFiles as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        require_once ($_SERVER['DOCUMENT_ROOT'] . $file);
    }
}

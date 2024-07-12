<?php

$requireFiles = [
    '/bitrix/vendor/autoload.php',
    '/local/php_interface/constants.php',
    '/local/php_interface/events.php',
    '/local/php_interface/scripts.php',
];

foreach ($requireFiles as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        require_once ($_SERVER['DOCUMENT_ROOT'] . $file);
    }
}

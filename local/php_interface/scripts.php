<?php

$arJsConfig = array(
    'booking' => array(
        'js' => '/local/js/otus/props/booking/script.js',
        'css' => '',
        'rel' => array(),
    )
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

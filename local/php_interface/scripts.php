<?php

/** @global CMain $APPLICATION */

use Bitrix\Main\Page\Asset;

$arJsConfig = [
    'booking' => [
        'js' => '/local/js/otus/props/booking/script.js',
        'css' => '',
        'rel' => [],
    ],
    'startWorkingDay' => [
        'js' => '/local/js/otus/StartWorkingDay/script.js',
        'css' => '',
        'rel' => [],
    ],
    'ClientAutoManager' => [
        'js' => '/local/js/otus/ClientAutoManager/script.js',
        'css' => '/local/js/otus/ClientAutoManager/style.css',
        'rel' => [],
    ],
    'CrmContact' => [
        'js' => '/local/js/otus/Crm/Contact/script.js',
        'css' => '/local/js/otus/Crm/Contact/style.css',
        'rel' => [],
    ],
    'getDealAutoById' => [
        'js' => '/local/js/namespace/getDealAutoById.js',
        'css' => '/local/js/otus/Crm/Contact/style.css',
        'rel' => [],
    ],
];

// Регистрация всех скриптов
foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

// Инициализация базового скрипта для рабочего дня
CJSCore::Init(['startWorkingDay']);

// Получение текущего URL страницы
$current_url = $APPLICATION->GetCurPage();


// Проверка, на какой странице находится пользователь
if (preg_match('/^\/crm\/deal\/details\/(\d+)\/$/', $current_url)) {
    // Инициализация скрипта для страницы сделки
    CJSCore::Init(['getDealAutoById']);
    CJSCore::Init(['ClientAutoManager']);
} elseif (preg_match('/^\/crm\/contact\/details\/(\d+)\/$/', $current_url)) {
    // Инициализация скрипта для страницы контакта
    CJSCore::Init(['getDealAutoById']);
    CJSCore::Init(['CrmContact']);
}

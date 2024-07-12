<?php

$eventManager = Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['Otus\Events\Booking', 'GetUserTypeDescription']
);

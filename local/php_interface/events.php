<?php

use \Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['Otus\Events\Booking', 'GetUserTypeDescription']
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementAdd',
    ['Otus\Events\Applications', 'addAfter']
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    ['Otus\Events\Applications', 'updateAfter']
);

$eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealAdd',
    ['Otus\Events\DealApplications', 'addAfter']
);

$eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealUpdate',
    ['Otus\Events\DealApplications', 'updateAfter']
);

$eventManager->addEventHandlerCompatible(
    'rest',
    'OnRestServiceBuildDescription',
    ['Otus\RestService\RestServiceCRUD', 'OnRestServiceBuildDescriptionHandler']
);

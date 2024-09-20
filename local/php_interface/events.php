<?php

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['Otus\Events\Booking', 'GetUserTypeDescription'],
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementAdd',
    ['Otus\Events\Applications', 'addAfter'],
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    ['Otus\Events\Applications', 'updateAfter'],
);

$eventManager->addEventHandler(
    'iblock',
    'OnBeforeIBlockElementDelete',
    ['Otus\Events\Applications', 'deleteDeal'],
);

$eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealAdd',
    ['Otus\Events\DealApplications', 'addAfter'],
);

$eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealUpdate',
    ['Otus\Events\DealApplications', 'updateAfter'],
);

$eventManager->addEventHandler(
    'crm',
    'OnBeforeCrmDealDelete',
    ['Otus\Events\DealApplications', 'deleteApplication'],
);

$eventManager->addEventHandlerCompatible(
    'rest',
    'OnRestServiceBuildDescription',
    ['Otus\RestService\RestServiceCRUD', 'OnRestServiceBuildDescriptionHandler'],
);

$eventManager->addEventHandler(
    'main',
    'OnUserTypeBuildList',
    ['\Otus\UserProperty\CrmDealCars', 'GetUserTypeDescription'],
);

$eventManager->addEventHandler('crm', 'onEntityDetailsTabsInitialized', [
    '\Otus\Crm\CrmTabs',
    'setCustomTabs',
]);

$eventManager->AddEventHandler('crm', 'OnBeforeCrmDealAdd', [
    '\Otus\Crm\Deal\OnBeforeCrmDealAdd\CrmDealAutoValidator',
    'onBeforeDealSave'
]);

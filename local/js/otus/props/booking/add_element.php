<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
@define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

global $USER;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$fio = $request['fio'];
$date = $request['date'];
$procedureId = $request['procedureId'];

$dateString = $date;

if (strpos($dateString, ' ') === false) {
    $dateString .= ' 00:00:00';
}

$date = DateTime::createFromFormat('d.m.Y H:i:s', $dateString);
$formattedDate = $date->format('Y-m-d H:i:s');

$booking = \Bitrix\Iblock\Elements\ElementBookingTable::getList([
    'select' => [
        'DATE' => 'VREMYA.VALUE',
        'PROTSEDURA_ID' => 'PROTSEDURA.IBLOCK_GENERIC_VALUE'
    ],
    'filter' => [
        '=DATE' => $formattedDate,
        '=PROTSEDURA_ID' => $procedureId
    ]
])->fetchAll();

if (count($booking) > 0) {
    echo \Bitrix\Main\Web\Json::encode(['success' => false, 'message' => 'Время занято!']);
} else {
    $el = new CIBlockElement;
    $PROP = [];
    $PROP[75] = $procedureId;
    $PROP[74] = $formattedDate;
    $arLoadProductArray = [
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => IBLOCK_ID_OTUS_BOOKING,
        "PROPERTY_VALUES" => $PROP,
        "NAME" => $fio,
        "ACTIVE" => "Y"
    ];

    $el->Add($arLoadProductArray);

    echo \Bitrix\Main\Web\Json::encode(['success' => true, [
        '=DATE' => $date,
        '=PROTSEDURA_ID' => $procedureId
    ], $request]);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

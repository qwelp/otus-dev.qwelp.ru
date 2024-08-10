<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$activityId = intval($_REQUEST['data']['FIELDS']['ID']);

if ($_REQUEST['event'] == 'ONCRMACTIVITYADD' &&  $activityId) {
    \Otus\Crm\Contact\CustomProperty::updateDateLastCommunication($activityId);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

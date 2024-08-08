<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

Bitrix\Main\Diag\Debug::writeToFile($_REQUEST, 'title', DEBUG_FILE_NAME);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$date = date("d.m.Y H:i:s");

Bitrix\Main\Diag\Debug::writeToFile($date, 'Дата и время', DEBUG_FILE_NAME);

?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
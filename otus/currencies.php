<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Компонент списка таблицы БД // ДЗ");
?><?$APPLICATION->IncludeComponent(
	"otusdev:currencie.views", 
	".default", 
	array(
		"LIST_CURRENCY" => "840",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule('currency');

$currency = Bitrix\Currency\CurrencyTable::getList()->fetchAll();

$arCurrencies = array_reduce($currency, function ($acc, $currency) {
    $acc[$currency['NUMCODE']] = $currency['CURRENCY'];
    return $acc;
}, []);

$arComponentParameters = array(
    "GROUPS" => array(
        "LIST" => array(
            "NAME" => "Настройки валюты",
            "SORT" => "300"
        )
    ),
    "PARAMETERS" => array(
        "LIST_CURRENCY"   =>  array(
            "PARENT"    =>  "LIST",
            "NAME"      =>  "Список",
            "TYPE"      =>  "LIST",
            "VALUES"    =>  $arCurrencies,
            "MULTIPLE"  =>  "N",
        ),
    )
);

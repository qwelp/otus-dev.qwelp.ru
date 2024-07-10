<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
    "NAME" => 'Показать валюту',
    "DESCRIPTION" => 'Курс валюты по умолчанию',
    "ICON" => "/images/news_list.gif",
    "SORT" => 20,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "otusdev",
        "CHILD" => array(
            "ID" => "currencie",
            "NAME" => GetMessage("NAME"),
            "SORT" => 10,
            "CHILD" => array(
                "ID" => "views",
            ),
        ),
    ),
);

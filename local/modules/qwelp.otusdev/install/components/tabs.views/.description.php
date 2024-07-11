<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
    "NAME" => 'Подключить вкладку',
    "DESCRIPTION" => 'Кастомная вкладка с компонентом',
    "ICON" => "/images/news_list.gif",
    "SORT" => 30,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "otusdev",
        "CHILD" => array(
            "ID" => "tab",
            "NAME" => GetMessage("NAME"),
            "SORT" => 10,
            "CHILD" => array(
                "ID" => "views",
            ),
        ),
    ),
);

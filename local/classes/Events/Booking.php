<?php

namespace Otus\Events;

use BItrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;
use \Qwelp\Otusdev\Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;

class Booking
{
    /**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return array(
            'USER_TYPE' => 'booking_iblock',
            'CLASS_NAME' => __CLASS__,
            "DESCRIPTION" => 'Бронирование',
            'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
            'GetAdminListViewHTML' => [__CLASS__, 'GetAdminListViewHTML'],
            'GetPublicViewHTML' => [__CLASS__, 'GetPublicViewHTML'],
        );
    }

    public static function  GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        Loader::includeModule('calendar');
        Loader::includeModule('qwelp.otusdev');
        \CUtil::InitJSCore(['booking']);

        $doctor = DoctorsTable::getByPrimary($arProperty['ELEMENT_ID'], [
            'select' => [
                'PROCEDURES'
            ]
        ])->fetch();

        $elementId = $arProperty['ELEMENT_ID'];

        $procedures = $doctor['PROCEDURES'];

        $html = [];
        foreach ($procedures as $procedureId => $procedureName) {
            $html[] = "<a href='#' class='booking-add-button' data-procedure-id='" . $procedureId . "'>{$procedureName}</a>";
        }

        return implode("<br>", $html);
    }

    public static function  GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return '&nbsp; 11';
    }

    /**
     * Конвертация данных перед сохранением в БД
     * @param $arProperty
     * @param $value
     * @return mixed
     */
    public static function ConvertToDB($arProperty, $value)
    {
        return $value['VALUE'];
    }

    /**
     * Конвертируем данные при извлечении из БД
     * @param $arProperty
     * @param $value
     * @param string $format
     * @return mixed
     */
    public static function ConvertFromDB($arProperty, $value, $format = '')
    {
        return $value['VALUE'];
    }

    /**
     * Представление формы редактирования значения
     * @param $arUserField
     * @param $arHtmlControl
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
    {
        return '4444';
    }
}
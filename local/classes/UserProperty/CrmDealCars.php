<?php

namespace Otus\UserProperty;

use Bitrix\Iblock\Elements\ElementGarageTable;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CrmDealCars
{
    /**
     * Возвращает описание пользовательского типа поля.
     *
     * @return array Массив с описанием пользовательского поля.
     */
    public static function GetUserTypeDescription()
    {
        return [
            "USER_TYPE_ID" => 'CrmDealCars',
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => Loc::getMessage('OTUS_CRM_DEAL_CARS_DESCRIPTION'),
            "BASE_TYPE" => \CUserTypeManager::BASE_TYPE_STRING,
            'EDIT_CALLBACK' => [
                __CLASS__,
                'GetPublicEdit',
            ],
            'VIEW_CALLBACK' => [
                __CLASS__,
                'GetPublicView',
            ],
        ];
    }

    /**
     * Формирует HTML для редактирования пользовательского поля.
     *
     * @param array $arUserField Данные о пользовательском поле.
     * @param array $arAdditionalParameters Дополнительные параметры.
     *
     * @return string HTML-код для отображения кнопки добавления автомобиля и скрытого поля с его значением.
     */
    public static function GetPublicEdit($arUserField, $arAdditionalParameters)
    {
        Extension::load('ui.buttons');
        $fieldName = static::getFieldName($arUserField, $arAdditionalParameters);

        // Проверяем, является ли $arUserField['VALUE'] массивом
        $value = $arUserField['VALUE'];
        if (is_array($value)) {
            // Если массив, объединяем элементы в строку
            $value = implode(', ', $value);
        }

        return '
            <a id="client-auto-manager-button-add" href="#" class="ui-btn ui-btn-primary">' . Loc::getMessage('OTUS_CRM_DEAL_CARS_ADD_BUTTON') . '</a>
            <input type="hidden" name="' . $fieldName . '" value="' . htmlspecialchars($value) . '"/>
        ';
    }

    /**
     * Возвращает имя поля для HTML формы.
     *
     * @param array $arUserField Данные о пользовательском поле.
     * @param array $arAdditionalParameters Дополнительные параметры (необязательно).
     *
     * @return string Имя поля.
     */
    protected static function getFieldName($arUserField, $arAdditionalParameters = array())
    {
        $fieldName = $arUserField["FIELD_NAME"];
        if($arUserField["MULTIPLE"] == "Y")
        {
            $fieldName .= "[]";
        }

        return $fieldName;
    }

    /**
     * Формирует HTML для отображения пользовательского поля.
     *
     * @param array $arUserField Данные о пользовательском поле.
     * @param array $arAdditionalParameters Дополнительные параметры (необязательно).
     *
     * @return string HTML-код для отображения данных об автомобиле или сообщение, если данные отсутствуют.
     */
    public static function GetPublicView($arUserField, $arAdditionalParameters = [])
    {
        // Проверяем, есть ли значение в поле
        if (empty($arUserField['VALUE'][0])) {
            // Если значение пустое, выводим текст по умолчанию
            return self::getEmptyCaption();
        }

        $html = "";

        if (\CBitrixComponent::includeComponentClass("otusdev:client.auto.manager")) {
            $clientAutoManeger = new \ClientAutoManagerComponent();
            $auto = $clientAutoManeger->getClientAutoByIdAction(['autoId' => $arUserField['VALUE'][0]]);

            $year = (int)$auto['YEAR_VALUE'];

            $html .= "<div class='crm-entity-widget-participants-block'>";
            $html .= "<div class='crm-entity-widget-inner crm-entity-widget-inner_client-auto-manager'>";
            $html .= "<ul class='client-auto-manager__items'>";
            $html .= "<li>" . Loc::getMessage('OTUS_CRM_DEAL_CARS_BRAND') . ": {$auto['NAME']}</li>";
            $html .= "<li>" . Loc::getMessage('OTUS_CRM_DEAL_CARS_MODEL') . ": {$auto['MODEL_VALUE']}</li>";
            $html .= "<li>" . Loc::getMessage('OTUS_CRM_DEAL_CARS_YEAR') . ": {$year}</li>";
            $html .= "<li>" . Loc::getMessage('OTUS_CRM_DEAL_CARS_COLOR') . ": {$auto['TSVET_VALUE']}</li>";
            $html .= "<li>" . Loc::getMessage('OTUS_CRM_DEAL_CARS_MILEAGE') . ": {$auto['PROBEG_VALUE']}</li>";
            $html .= "<li><a href=\"#\" class=\"view-history\" data-row-id=\"{$auto['ID']}\" data-model=\"{$auto['NAME']}\">" . Loc::getMessage('OTUS_CRM_DEAL_CARS_VIEW_HISTORY') . "</a></li>";
            $html .= "</ul>";
            $html .= "</div>";
            $html .= "</div>";
        }

        return $html;
    }

    /**
     * Возвращает текст по умолчанию, если значение поля не задано.
     *
     * @return string Текст по умолчанию.
     */
    public static function getEmptyCaption()
    {
        return Loc::getMessage('OTUS_CRM_DEAL_CARS_NO_VALUE');
    }

    /**
     * Возвращает тип колонки для хранения значения в базе данных.
     *
     * @return string Тип данных для хранения в БД (text).
     */
    public static function GetDBColumnType()
    {
        return "text";
    }

    /**
     * Вызывается перед сохранением пользовательского поля в базу данных.
     *
     * @param array $arUserField Данные о пользовательском поле.
     * @param mixed $value Значение поля.
     *
     * @return mixed Значение для сохранения в базе данных.
     */
    public static function OnBeforeSave($arUserField, $value)
    {
        if (is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    }

    /**
     * Вызывается после получения данных из базы данных для формирования массива значений.
     *
     * @param array $userfield Данные о пользовательском поле.
     * @param array $fetched Полученные данные из БД.
     *
     * @return array Массив значений для пользовательского поля.
     */
    public static function onAfterFetch($userfield, $fetched)
    {
        if (!empty($fetched["VALUE"])) {
            return explode(',', $fetched["VALUE"]);
        }

        return $fetched["VALUE"];
    }

    /**
     * Возвращает HTML для редактирования поля в форме.
     *
     * @param array $arUserField Данные о пользовательском поле.
     * @param array $arHtmlControl HTML-элементы для управления полем.
     *
     * @return string HTML-код для отображения поля редактирования.
     */
    public static function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        return 'GetEditFormHTML';
    }
}

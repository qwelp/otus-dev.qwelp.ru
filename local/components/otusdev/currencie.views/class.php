<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $this */

/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;

class CurrencieComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function checkModules()
    {
        if (!Loader::includeModule('currency')) {
            throw new \Exception("Не загружены модули необходимые для работы компонента");
        }
        return true;
    }

    public static function getCurrencies(): array
    {
        return \Bitrix\Currency\CurrencyTable::getList()->fetchAll();
    }

    public function getCurrencyDefault(): array
    {
        $paramCurrencyDefault = $this->arParams['LIST_CURRENCY'];
        $currencyDefault = array_filter(self::getCurrencies(), function ($currency) use ($paramCurrencyDefault) {
            return $currency['NUMCODE'] == $paramCurrencyDefault;
        });

        return current($currencyDefault);
    }

    public function executeComponent()
    {
        $this->checkModules();

        try {
            $this->arResult = $this->getCurrencyDefault();
            $this->IncludeComponentTemplate();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }
}

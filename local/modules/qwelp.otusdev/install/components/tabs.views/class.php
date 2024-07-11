<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var CBitrixComponent $this */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;
use Bitrix\Main\Grid\Options;

class TabsViewsComponent extends \CBitrixComponent
{
    const GRID_ID = 'cars_grid_id';

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function checkModules()
    {
        if (!Loader::includeModule('qwelp.otusdev')) {
            throw new \Exception("Не загружены модули необходимые для работы компонента");
        }
        return true;
    }

    public static function getColumns(): array
    {
        return [
            ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
            ['id' => 'NAME', 'name' => 'Марка', 'sort' => 'NAME', 'default' => true]
        ];
    }

    public static function getItems(): array
    {
        $gridOptions = new Options(static::GRID_ID);

        $sort = $gridOptions->GetSorting([
            'sort' => ['ID' => 'ASC']
        ]);

        $cars = \Qwelp\Otusdev\Models\Lists\CarsTable::query()
            ->setSelect(['ID', 'NAME'])
            ->setOrder($sort['sort'])
            ->fetchAll();

        $list = [];

        foreach ($cars as $car) {
            $list[] = [
                'id' => 'unique_row_id_' . $car['ID'],
                'data' => [
                    'ID' => $car['ID'],
                    'NAME' => $car['NAME']
                ]
            ];
        }
        return $list;
    }

    public function executeComponent()
    {
        $this->checkModules();

        try {
            $this->arResult['GRID_ID'] = static::GRID_ID;
            $this->arResult['COLUMNS'] = $this->getColumns();
            $this->arResult['ROWS'] = $this->getItems();
            $this->IncludeComponentTemplate();
        } catch (\Exception $e) {
            ShowError($e->getMessage());
        }
    }
}

<?php

namespace Otus\Crm\Contact;

use Bitrix\Main\Grid\Options;

class Garage
{
    public const GRID_ID = 'contact_garage_grid_id';

    public int $clientId;

    public function __construct(int $clientId)
    {
        $this->clientId = $clientId;
    }

    public function getColumns(): array
    {
        return [
            ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
            ['id' => 'NAME', 'name' => 'Марка', 'sort' => 'NAME', 'default' => true],
        ];
    }

    public function getItems(): array
    {
        $gridOptions = new Options(static::GRID_ID);

        $sort = $gridOptions->GetSorting([
            'sort' => ['ID' => 'ASC'],
        ]);

        $cars = \Bitrix\Iblock\Elements\ElementGarageTable::getList([
            'select' => ['ID', 'NAME', 'CLIENT_ID' => 'KLIENT.VALUE'],
            'filter' => ['CLIENT_ID' => $this->clientId],
            'order' => $sort['sort'],
        ])->fetchAll();

        $list = [];

        foreach ($cars as $car) {
            $list[] = [
                'id' => 'unique_row_id_' . $car['ID'],
                'data' => [
                    'ID' => $car['ID'],
                    'NAME' => $car['NAME']
                ],
            ];
        }
        return $list;
    }
}

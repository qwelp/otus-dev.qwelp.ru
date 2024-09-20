<?php

namespace Otus\Iblock;

use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementCatalogCrmTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Web\HttpClient;
use Otus\BusinessProcess\BusinessProcessManager;

/**
 * Класс для обновления количеств товаров на основе случайных значений.
 *
 * Пример использования
 * $updater = new ProductQuantityUpdater(13, 'https://www.random.org/integers/?num=1&min=0&max=10&col=1&base=10&format=plain&rnd=new');
 * $updater->updateProductQuantities();
 */
class ProductQuantityUpdater
{
    private const IBLOCK_ID = 27;
    private const TEMPLATE_ID = 30;
    private const DEPARTMENT_ID = 3;

    /**
     * ID инфоблока, для которого нужно обновить товары.
     *
     * @var int
     */
    private $iblockSectionId;

    /**
     * URL для получения случайных чисел.
     *
     * @var string
     */
    private $randomNumberUrl;

    /**
     * Конструктор класса.
     *
     * @param int $iblockSectionId ID инфоблока.
     * @param string $randomNumberUrl URL для получения случайных чисел.
     */
    public function __construct($iblockSectionId, $randomNumberUrl)
    {
        $this->iblockSectionId = $iblockSectionId;
        $this->randomNumberUrl = $randomNumberUrl;
    }

    /**
     * Обновляет количество товаров в инфоблоке на случайные значения.
     */
    public function updateProductQuantities(): void
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        // Получаем список продуктов
        $products = ElementCatalogCrmTable::getList([
            'select' => ['ID'],
            'filter' => ['IBLOCK_SECTION_ID' => $this->iblockSectionId],
        ])->fetchAll();

        $productIds = array_column($products, 'ID');

        $offerIds = [];
        foreach ($productIds as $productId) {
            $arSKU = \CCatalogSKU::getOffersList($productId);

            foreach ($arSKU as $parentId => $children) {
                foreach ($children as $childId => $data) {
                    $offerIds[] = $data['ID'];
                }
            }
        }

        // Создаем экземпляр HttpClient
        $httpClient = new HttpClient();

        $arRandQuantity = [];

        // Обновляем количество для каждого предложения
        $productsOutOfStock = [];
        foreach ($offerIds as $offerId) {
            $response = $httpClient->get($this->randomNumberUrl);
            $randQuantity = intval($response);

            $arRandQuantity[$offerId] = $randQuantity;

            ProductTable::update($offerId, [
                'QUANTITY' => $randQuantity,
            ]);

            // Если 0, то создаём заявку на покупку
            if (!$randQuantity) {
                $productsOutOfStock[] = $offerId;
            }
        }

        if ($productsOutOfStock) {
            $this->procurementRequest($productsOutOfStock);
        }
    }

    /**
     * Инициализирует запрос на закупку для товаров, которых нет в наличии.
     *
     * Метод запускает бизнес-процесс для отдела, который отвечает за закупки,
     * и передает список отсутствующих товаров в качестве параметра. Также
     * отправляет уведомление о запуске бизнес-процесса пользователям отдела.
     *
     * @param array $productsOutOfStock Массив товаров, которые отсутствуют в наличии.
     *
     * @return void
     */
    public function procurementRequest(array $productsOutOfStock): void
    {
        if (!count($productsOutOfStock)) {
            return;
        }

        // Получение списка пользователей в отделе
        $rsUsers = \CUser::GetList(
            ($by = "id"),
            ($order = "asc"),
            ["UF_DEPARTMENT" => self::DEPARTMENT_ID],
            ["SELECT" => ["ID", "NAME", "LAST_NAME"]],
        );

        $targetUsers = [];
        $targetUsersId = [];

        while ($user = $rsUsers->Fetch()) {
            $targetUsers[] = "user_" . $user["ID"];
            $targetUsersId[] = $user["ID"];
        }

        $parameters = [];
        $parameters['TargetUser'] = implode(',', $targetUsers);
        $parameters['Products'] = $productsOutOfStock;

        $businessProcess = new BusinessProcessManager(self::IBLOCK_ID, self::TEMPLATE_ID, $parameters);

        $businessProcess->startWorkflow();
        $businessProcess->logWorkflowStart($targetUsersId);
    }
}

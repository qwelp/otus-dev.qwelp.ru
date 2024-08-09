<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Main\Loader;

class CBPDz9Activity extends BaseActivity
{
    /**
     * @see parent::_construct()
     * @param $name string Activity name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Inn' => ''
        ];

        $this->SetPropertiesTypes([
            'Text' => ['Type' => FieldType::STRING],
        ]);
    }

    /**
     * Return activity file path
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * @return ErrorCollection
     */
    protected function internalExecute(): ErrorCollection
    {
        $errors = parent::internalExecute();

        $company =  self::getDaData($this->Inn);
        $companyName = $company[0]['value'];
        $rootActivity = $this->GetRootActivity();
        $documentId = $rootActivity->GetDocumentId(); // DEAL_4

        $this->preparedProperties['Text'] = $companyName;

        if ($this->preparedProperties['Text'] === null) {
            $this->log('Сообщение не найдено');
        } else {
            $this->log($this->preparedProperties['Text']);
        }

        $this->saveCompany($documentId, $companyName);

        return $errors;
    }

    private function saveCompany($documentId, $companyName)
    {
        Loader::includeModule("crm");
        Loader::includeModule("iblock");

        $dealId = explode("_", $documentId[2])[1];

        $dbDeal = CCrmDeal::GetListEx(
            [],
            ['ID' => $dealId],
            false,
            false,
            ['TYPE_ID', 'OPPORTUNITY', 'ASSIGNED_BY_ID']
        );

        if ($arDeal = $dbDeal->Fetch()) {
            $arFields = [
                "IBLOCK_ID" => IBLOCK_ID_ACCOUNTING_ORDERS,
                "NAME" => "Сделка №" . $dealId,
                "ACTIVE" => "Y",
                "PROPERTY_VALUES" => [
                    76 => $arDeal['OPPORTUNITY'],
                    77 => $this->Inn,
                    78 => $companyName,
                    79 => $arDeal['TYPE_ID']
                ]
            ];

            $element = new CIBlockElement;
            $element->Add($arFields);
        }

        $requisite = new \Bitrix\Crm\EntityRequisite();
        $requisiteList = $requisite->getList([
            'filter' => [
                'RQ_INN' => $this->Inn,
                'ENTITY_TYPE_ID' => \CCrmOwnerType::Company
            ],
            'select' => ['ENTITY_ID']
        ]);

        $companyIds = [];
        $companyId = 0;
        while ($requisiteItem = $requisiteList->fetch()) {
            $companyIds[] = $requisiteItem['ENTITY_ID'];
            $companyId = $requisiteItem['ENTITY_ID'];
        }

        if (empty($companyIds)) {
            $entityFields = [
                'TITLE'   => $companyName,
                'COMPANY_TYPE' => 'CUSTOMER',
                'CONTACT_ID' => [
                    1,
                ],
                "OPENED" => "Y",
                "ASSIGNED_BY_ID" => $arDeal['ASSIGNED_BY_ID'],
            ];

            $entityObject = new \CCrmCompany();
            $companyId = $entityObject->Add($entityFields);

            $requisiteFields = [
                "ENTITY_TYPE_ID" => CCrmOwnerType::Company,
                "ENTITY_ID" => $companyId,
                "PRESET_ID" => 1,
                "NAME" => "Основные реквизиты",
                "ACTIVE" => "Y",
                "RQ_INN" => $this->Inn,
                "ADDRESS" => [
                    "TYPE_ID" => 1,
                    "CITY" => "Можно найти CITY в dadata",
                    "ADDRESS_1" => "Можно найти адрес в dadata"
                ]
            ];

            $requisite = new \Bitrix\Crm\EntityRequisite();
            $requisite->add($requisiteFields);
        }

        $dealFields = [
            "COMPANY_ID" => $companyId
        ];

        $deal = new CCrmDeal;
        $deal->Update($dealId, $dealFields);
    }

    public static function getDaData($inn)
    {
        $token = "8015ef1ec112e1587de6e6810874825e8ee687db";
        $secret = "1b37271baddd06da1c2deafbcc1adc3a9d0cf48d";
        $dadata = new \Dadata\DadataClient($token, $secret);

        $response = $dadata->findById("party", $inn);

        return $response;
    }

    /**
     * @param PropertiesDialog|null $dialog
     * @return array[]
     */
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'Inn' => [
                'Name' => Loc::getMessage('DZ9_ACTIVITY_FIELD_INN'),
                'FieldName' => 'Inn',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Options' => [],
            ],
        ];
        return $map;
    }
}

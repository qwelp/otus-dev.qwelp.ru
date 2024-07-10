<?php

namespace Qwelp\Otusdev\Models\Lists;

use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Qwelp\Otusdev\Models\AbstractIblockPropertyValuesTable;
use Qwelp\Otusdev\Models\Lists\DoctorsPropertyValuesTable;

class ProceduresPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    const IBLOCK_ID = IBLOCK_ID_PROCEDURES;
}

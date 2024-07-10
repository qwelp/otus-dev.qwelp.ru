<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

/** @global CMain $APPLICATION */

$APPLICATION->SetTitle("Создание своих таблиц БД и написание модели данных к ним // ДЗ");

use Bitrix\Main\Loader;
use Qwelp\Otusdev\Models\Lists\ProjectTasksTable;

Loader::includeModule('qwelp.otusdev');

$projectTasks = ProjectTasksTable::getList([
    'select' => [
        'PROJECT',
        'PROJECT.MANAGER',
        'TASK',
        'TASK.EMPLOYEE_IDS_MULTY.ELEMENT',
        'EMPLOYEE',
        'EMPLOYEE.POSITION',
        'EMPLOYEE.DEPARTMENT',
    ]
])->fetchCollection();

$projects = [];
foreach ($projectTasks as $projectTask) {
    $employees = [];
    foreach ($projectTask->getTask()->getEmployeeIdsMulty()->getAll() as $employee) {
        $employees[] = $employee->getElement()->getName();
    }

    $projects[] = [
        'PROJECT' => [
            'NAME' => $projectTask->getProject()->getName(),
            'MANAGER' => $projectTask->getProject()->getManager()->getValue()
        ],
        'TASK' => [
            'NAME' => $projectTask->getTask()->getName(),
            'EMPLOYEE_IDS_MULTY' => $employees
        ],
        'EMPLOYEE' => [
            'NAME' => $projectTask->getEmployee()->getName(),
            'POSITION' => $projectTask->getEmployee()->getPosition()->getValue(),
            'DEPARTMENT' => $projectTask->getEmployee()->getDepartment()->getValue(),
        ],
    ];
}

echo "<pre>";
print_r($projects);
echo "</pre>";

//ProjectTasks::dropTable();
//ProjectTasks::createTable();

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Vasoft\Core\Updater;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;

class TableInstaller
{
    private Connection|\Bitrix\Main\Data\Connection $connection;

    /**
     * @param string $moduleId Идентификатор модуля
     * @param string[] $tables Классы таблиц
     * @throws LoaderException
     */
    public function __construct(
        string                 $moduleId,
        private readonly array $tables
    )
    {
        Loader::includeModule($moduleId);
        $this->connection = Application::getConnection();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     */
    public function check(): void
    {
        foreach ($this->tables as $tableClass) {
            $instance = Base::getInstance($tableClass);
            if (!$this->connection->isTableExists($instance->getDBTableName())) {
                $instance->createDbTable();
            }
        }
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     */
    public function clean(): void
    {
        foreach ($this->tables as $tableClass) {
            $tableName = Base::getInstance($tableClass)->getDBTableName();
            if ($this->connection->isTableExists($tableName)) {
                $this->connection->queryExecute('drop table if exists ' . $tableName);
            }
        }
    }
}
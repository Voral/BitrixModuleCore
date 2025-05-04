<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;

class TableInstaller
{
    private \Bitrix\Main\Data\Connection|Connection $connection;

    /**
     * @param string   $moduleId Идентификатор модуля
     * @param string[] $tables   Классы таблиц
     *
     * @throws LoaderException
     */
    public function __construct(
        string $moduleId,
        private readonly array $tables,
    ) {
        Loader::includeModule($moduleId);
        $this->connection = Application::getConnection();
    }

    /**
     * @throws ArgumentException
     * @throws SystemException
     */
    public function check(): void
    {
        foreach ($this->tables as $tableClass) {
            $instance = Entity::getInstance($tableClass);
            if (!$this->connection->isTableExists($instance->getDBTableName())) {
                $instance->createDbTable();
            }
        }
    }

    /**
     * @throws ArgumentException
     * @throws SystemException
     */
    public function clean(): void
    {
        foreach ($this->tables as $tableClass) {
            $tableName = Entity::getInstance($tableClass)->getDBTableName();
            if ($this->connection->isTableExists($tableName)) {
                $this->connection->queryExecute('drop table if exists ' . $tableName);
            }
        }
    }
}

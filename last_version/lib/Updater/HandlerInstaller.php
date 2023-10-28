<?php

namespace Vasoft\Core\Updater;

use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\EventManager;

class HandlerInstaller
{
    private EventManager $manager;
    public readonly array $handlers;

    public function __construct(
        public readonly string $moduleId,
        array                  $handlers
    )
    {
        $this->handlers = array_map(static fn(HandlerDto $dto) => [
            'FROM_MODULE_ID' => $dto->emmitModuleId,
            'MESSAGE_ID' => $dto->messageId,
            'TO_CLASS' => $dto->receiverClass,
            'TO_METHOD' => $dto->receiverMethod
        ], $handlers);
        $this->manager = EventManager::getInstance();
    }

    /**
     * Проверка обработчиков событий.
     * Создает обработчики, которых нет, и удаляет те, которые устарели
     * @return void
     * @throws SqlQueryException
     */
    public function check(): void
    {
        $exists = array_reduce($this->getExists(), [$this, 'indexer'], []);
        $needed = array_reduce($this->handlers, [$this, 'indexer'], []);
        $create = array_diff_key($needed, $exists);
        $delete = array_diff_key($exists, $needed);
        array_walk($create, [$this, 'register']);
        array_walk($delete, [$this, 'unregister']);
    }
    /**
     * Удаление всех обработчиков модуля
     * @return void
     * @throws SqlQueryException
     */
    public function clean(): void
    {
        $exists = array_reduce($this->getExists(), [$this, 'indexer'], []);
        array_walk($exists, [$this, 'unregister']);
    }

    private function unregister(array $data): void
    {
        $this->manager->unRegisterEventHandler(
            $data['FROM_MODULE_ID'],
            $data['MESSAGE_ID'],
            $this->moduleId,
            $data['TO_CLASS'],
            $data['TO_METHOD']
        );
    }

    private function register(array $data): void
    {
        $this->manager->registerEventHandler(
            $data['FROM_MODULE_ID'],
            $data['MESSAGE_ID'],
            $this->moduleId,
            $data['TO_CLASS'],
            $data['TO_METHOD']
        );
    }

    private function indexer(array $result, array $curry): array
    {
        $result[implode('##', [
            $curry['FROM_MODULE_ID'],
            $curry['MESSAGE_ID'],
            $curry['TO_CLASS'],
            $curry['TO_METHOD'],
        ])] = $curry;
        return $result;
    }

    /**
     * @return array
     * @throws SqlQueryException
     * @noinspection SqlResolve
     * @noinspection SqlNoDataSourceInspection
     * @noinspection SqlDialectInspection
     */
    private function getExists(): array
    {
        $con = Application::getConnection();
        $rs = $con->query(sprintf(
            "SELECT FROM_MODULE_ID, MESSAGE_ID, SORT, TO_CLASS, TO_METHOD  FROM b_module_to_module	WHERE TO_MODULE_ID = '%s'",
            $this->moduleId
        ));
        return $rs->fetchAll();
    }
}
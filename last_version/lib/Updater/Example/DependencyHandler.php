<?php /** @noinspection PhpUnused */

namespace Vasoft\Core\Updater\Example;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class DependencyHandler
{
    /**
     * Предотвращение удаления модуля vasoft.core
     * @param Event $event
     * @return EventResult
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    public static function onBeforeRemoveVasoftCore(Event $event): EventResult
    {
        /**
         * Возвращаем наименование модуля вторым параметром
         * И идентификатор модуля третьим
         */
        return new EventResult(EventResult::ERROR, 'Example module name', 'vendor.module');
    }
}
<?php

declare(strict_types=1);
/** @noinspection PhpUnused */

namespace Vendor\Example\updater;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class DependencyHandler
{
    /**
     * Предотвращение удаления модуля vasoft.core.
     *
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    public static function onBeforeRemoveVasoftCore(Event $event): EventResult
    {
        /*
         * Возвращаем наименование модуля вторым параметром
         * И идентификатор модуля третьим
         */
        return new EventResult(EventResult::ERROR, 'Example module name', 'vendor.module');
    }
}

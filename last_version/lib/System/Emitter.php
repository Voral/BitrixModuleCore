<?php

declare(strict_types=1);

namespace Vasoft\Core\System;

use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use Vasoft\Core\Exceptions\DependencyExistsException;
use Bitrix\Main\EventResult;

class Emitter
{
    public const EVENT_MODULE_REMOVE = 'onBeforeRemoveVasoftCore';

    /**
     * @throws DependencyExistsException
     */
    public static function emitRemove(): void
    {
        $event = new Event('vasoft.core', self::EVENT_MODULE_REMOVE, []);
        $event->send();
        $dependency = [];
        foreach ($event->getResults() as $eventResult) {
            if (EventResult::ERROR === $eventResult->getType()) {
                $title = trim($eventResult->getParameters());
                $moduleId = trim($eventResult->getModuleId());
                if ('' === $title && '' === $moduleId) {
                    $dependency[] = Loc::getMessage('UNKNOWN_DEPENDENCY_MODULE');
                } elseif ('' === $title) {
                    $dependency[] = $moduleId;
                } elseif ('' === $moduleId) {
                    $dependency[] = $title;
                } else {
                    $dependency[] = sprintf('%s (%s)', $title, $moduleId);
                }
            }
        }
        if (!empty($dependency)) {
            throw new DependencyExistsException($dependency);
        }
    }
}

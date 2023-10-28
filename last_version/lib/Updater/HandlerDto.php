<?php

namespace Vasoft\Core\Updater;

class HandlerDto
{
    public function __construct(
        public readonly string $emmitModuleId,
        public readonly string $messageId,
        public readonly string $receiverClass,
        public readonly string $receiverMethod,
    )
    {
    }
}
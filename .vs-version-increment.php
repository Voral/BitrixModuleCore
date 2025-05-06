<?php

declare(strict_types=1);

use Vasoft\VersionIncrement\Config;

return (new Config())
    ->setHideDoubles(true)
    ->setEnabledComposerVersioning(false);

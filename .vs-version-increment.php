<?php

declare(strict_types=1);

use Vasoft\VersionIncrement\Config;

return (new Config())
    ->setSection('test', 'Tests', hidden: true)
    ->setSection('docs', 'Documentation', hidden: true)
    ->setSection('chore', 'Other changes', hidden: true)
    ->setHideDoubles(true)
    ->setEnabledComposerVersioning(false);

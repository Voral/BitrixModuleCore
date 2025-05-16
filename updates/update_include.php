<?php

\Bitrix\Main\Loader::includeModule('vasoft.core');
(new \Vasoft\Core\Handlers\HandlerUpdater())->check();

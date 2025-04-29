<?php

declare(strict_types=1);
/**
 * Запуск без параметров - удаление
 * с параметром install - установка
 * с параметром options - экспорт/импорт настроек.
 */

// @noinspection PhpDefineCanBeReplacedWithConstInspection

// @noinspection DuplicatedCode

use Bitrix\Main\Loader;
use Vasoft\Core\Settings\Example\ExampleModuleSettings;
use Vasoft\Core\Updater\Example\DependencyHandler;
use Vasoft\Core\Updater\Example\ExampleTable;
use Vasoft\Core\Updater\FileInstaller;
use Vasoft\Core\Updater\HandlerDto;
use Vasoft\Core\Updater\HandlerInstaller;
use Vasoft\Core\Updater\OptionsDump;
use Vasoft\Core\Updater\TableInstaller;

if (PHP_SAPI !== 'cli') {
    exit;
}
$mode = trim($argv[1] ?? 'remove');

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__DIR__, 6));
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
define('NOT_CHECK_PERMISSIONS', true);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

@set_time_limit(0);
@ignore_user_abort(true);
ini_set('implicit_flush', 1);
ob_implicit_flush(true);
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');

$moduleId = 'vasoft.core';
Loader::includeModule($moduleId);
$filePrefix = str_replace('.', '_', $moduleId) . '_';
$filesPath = __DIR__;
$fileInstaller = new FileInstaller($filePrefix, __DIR__);
/**
 * В своих модулях рекомендую создавать обертку для этого класса содержащую перечень хендлеров
 * Не забудьте удалить после тестов тестовый обработчик запуском деинсталяции (без клчюей).
 */
$handlerInstaller = new HandlerInstaller($moduleId, [
    new HandlerDto(
        'vasoft.core',
        'onBeforeRemoveVasoftCore',
        DependencyHandler::class,
        'onBeforeRemoveVasoftCore',
    ),
]);
$tableInstaller = new TableInstaller($moduleId, [
    ExampleTable::class,
]);

if ('install' === $mode) {
    /*
     * Будут установлены все файлы из каталога ./admin
     * имена ./admin/[имя файла] -> /bitrix/admin/[$filePrefix][имя файла]
     * Команда в этом примере создаст файл /bitrix/admin/vasoft_core_example.php
     */
    $fileInstaller->checkAdminPages(true);
    $handlerInstaller->check();
    $tableInstaller->check();
} elseif ('options' === $mode) {
    $optionsDumper = new OptionsDump(__DIR__, '#MODULE_ID#');
    $settings = ExampleModuleSettings::getInstance();

    // Создаем файл дампа
    $optionsDumper->dumpSettings($settings);

    // Эмулируем другую версию
    $fileName = 'vasoft.core.example.php';
    $options = [];
    include_once $fileName;
    $options[ExampleModuleSettings::PROP_EXAMPLE_TEXT] .= "\nOptions test added at " . date(
        'd.m.Y H:i:s',
    );
    $file = fopen($fileName, 'w');
    if ($file) {
        fwrite($file, '<?php' . PHP_EOL);
        fwrite($file, '$options = ' . var_export($options, true) . ';' . PHP_EOL);
        fclose($file);
    }

    // Восстанавливаем из дампа
    $optionsDumper->restoreSettings($settings, $fileName, backup: false);

    // Удаляем тестовый мусор
    unlink($fileName);
} else {
    $fileInstaller->cleanAdminPages();
    $handlerInstaller->clean();
    $tableInstaller->clean();
}

class ExampleHandlers
{
    public static function handler(): void {}
}

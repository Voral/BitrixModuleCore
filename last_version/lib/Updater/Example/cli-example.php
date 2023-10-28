<?php
/**
 * Запуск без параметров - удаление
 * с параметром install - установка
 * с параметром options - экспорт/импорт настроек
 */
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

/** @noinspection DuplicatedCode */

use Bitrix\Main\Loader;


if (PHP_SAPI !== 'cli') {
    die();
}
$mode = trim($argv[1] ?? 'remove');

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__DIR__, 6));
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

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
$fileInstaller = new \Vasoft\Core\Updater\FileInstaller($filePrefix, __DIR__);
/**
 * В своих модулях рекомендую создавать обертку для этого класса содержащую перечень хендлеров
 * Не забудьте удалить после тестов тестовый обработчик запуском деинсталяции (без клчюей)
 */
$handlerInstaller = new \Vasoft\Core\Updater\HandlerInstaller($moduleId, [
    new \Vasoft\Core\Updater\HandlerDto(
        'vasoft.core',
        'onBeforeRemoveVasoftCore',
        \Vasoft\Core\Updater\Example\DependencyHandler::class,
        'onBeforeRemoveVasoftCore'
    )
]);
$tableInstaller = new \Vasoft\Core\Updater\TableInstaller($moduleId, [
    \Vasoft\Core\Updater\Example\ExampleTable::class
]);

if ($mode === 'install') {
    /**
     * Будут установлены все файлы из каталога ./admin
     * имена ./admin/[имя файла] -> /bitrix/admin/[$filePrefix][имя файла]
     * Команда в этом примере создаст файл /bitrix/admin/vasoft_core_example.php
     */
    $fileInstaller->checkAdminPages(true);
    $handlerInstaller->check();
    $tableInstaller->check();
} elseif ($mode === 'options') {
    $optionsDumper = new \Vasoft\Core\Updater\OptionsDump(__DIR__, '#MODULE_ID#');
    $settings = \Vasoft\Core\Settings\Example\ExampleModuleSettings::getInstance();

    // Создаем файл дампа
    $optionsDumper->dumpSettings($settings);

    // Эмулируем другую версию
    $fileName = 'vasoft.core.example.php';
    $options = [];
    include_once $fileName;
    $options[\Vasoft\Core\Settings\Example\ExampleModuleSettings::PROP_EXAMPLE_TEXT] .= "\nOptions test added at " . date('d.m.Y H:i:s');
    $file = fopen($fileName, 'wb');
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
    public static function handler(): void
    {

    }
}
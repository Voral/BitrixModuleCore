<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Vasoft\Core\Settings\ModuleSettings;

class OptionsDump
{
    private string $baseDir;

    public function __construct(
        string $baseDir = '',
        protected readonly string $fileNameTemplate = '#MODULE_ID#_#TIME#',
    ) {
        $this->baseDir = rtrim(
            '' === $baseDir ? $_SERVER['DOCUMENT_ROOT'] : $baseDir,
            " \t\n\r\0\x0B" . \DIRECTORY_SEPARATOR,
        ) . \DIRECTORY_SEPARATOR;
    }

    /**
     * Настройки класса конфигурации модуля в php файл.
     *
     * @param ModuleSettings $settings Объект конфигурации
     * @param array          $filter   Массив символьных кодов настроек, которы необходимо исключить
     *
     * @throws ArgumentNullException
     */
    public function dumpSettings(ModuleSettings $settings, array $filter = []): void
    {
        $this->dump(
            $settings->moduleCode,
            $this->getFileName($settings->moduleCode, $this->fileNameTemplate),
            $filter,
            $settings->siteId,
        );
    }

    public function getFileName(string $moduleId, string $template): string
    {
        return $this->baseDir . str_replace(
            ['#MODULE_ID#', '#TIME#'],
            [$moduleId, date('YmdHis')],
            $template,
        );
    }

    /**
     * Настройки модуля в php файл.
     *
     * @param string       $moduleId Идентификатор модуля
     * @param array        $filter   Массив символьных кодов настроек, которы необходимо исключить
     * @param false|string $siteId   Идентификатор сайта или false для сайта по умолчанию
     *
     * @throws ArgumentNullException
     */
    public function dump(string $moduleId, string $fileName, array $filter = [], false|string $siteId = false): void
    {
        //        OptionCacheCleaner::clearModuleCache($moduleId);
        $options = Option::getForModule($moduleId, $siteId);
        $options = array_diff_key($options, array_flip($filter));
        $file = fopen($fileName . '.php', 'w');
        if ($file) {
            fwrite($file, '<?php' . PHP_EOL);
            fwrite($file, '$options = ' . var_export($options, true) . ';' . PHP_EOL);
            fclose($file);
        }
    }

    /**
     * Восстановление настроек модуля из php файла.
     *
     * @param ModuleSettings $settings Идентификатор модуля
     * @param array          $filter   Массив символьных кодов настроек, которы необходимо исключить
     * @param bool           $backup   Выполнять бекап
     *
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function restoreSettings(
        ModuleSettings $settings,
        string $fileName,
        array $filter = [],
        bool $backup = true,
    ): void {
        if (file_exists($fileName)) {
            $options = [];
            include $fileName;
            $options = array_diff_key($options, array_flip($filter));
            if ($backup && !empty($options)) {
                $this->dump(
                    $settings->moduleCode,
                    $this->getFileName($settings->moduleCode, '#MODULE_ID#-#TIME#-bkp'),
                    [],
                    $settings->siteId,
                );
            }
            $settings->saveFromArray($options);
        }
    }

    /**
     * Восстановление настроек модуля из php файла.
     *
     * @param string      $moduleId Идентификатор модуля
     * @param array       $filter   Массив символьных кодов настроек, которы необходимо исключить
     * @param bool|string $siteId   Идентификатор сайта или false для сайта по умолчанию
     * @param bool        $backup   Выполнять бекап
     *
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function restore(
        string $moduleId,
        string $fileName,
        array $filter = [],
        bool|string $siteId = false,
        bool $backup = true,
    ): void {
        if (file_exists($fileName)) {
            $options = [];
            include_once $fileName;
            $options = array_diff_key($options, array_flip($filter));
            if ($backup && !empty($options)) {
                $this->dump(
                    $moduleId,
                    $this->getFileName($moduleId, '#MODULE_ID#-#TIME#-bkp'),
                    [],
                    $siteId,
                );
            }
            foreach ($options as $key => $value) {
                Option::set($moduleId, $key, $value, $siteId);
            }
            // OptionCacheCleaner::clearModuleCache($moduleId);
        }
    }
}

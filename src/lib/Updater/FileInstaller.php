<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\FileNotFoundException;
use Bitrix\Main\Loader;

class FileInstaller
{
    private readonly string $path;
    private readonly string $documentRoot;

    /**
     * @param string $filePrefix Префикс создаваемых в каталоге /bitrix/admin файлов
     * @param string $path       Путь в котором хранятся файлы, необходимые в админке
     */
    public function __construct(
        private readonly string $filePrefix,
        string $path,
    ) {
        $this->documentRoot = Loader::getDocumentRoot();
        $this->path = \DIRECTORY_SEPARATOR . trim(
            str_replace($this->documentRoot, '', $path),
            " \t\n\r\0\x0B" . \DIRECTORY_SEPARATOR,
        ) . \DIRECTORY_SEPARATOR;
    }

    /**
     * Создание необходимых и удаление устаревших страниц админки.
     *
     * @param bool $renew Пересоздать существующие файлы
     *
     * @throws FileNotFoundException
     */
    public function checkAdminPages(bool $renew = false): void
    {
        $section = 'admin/';
        $needed = $this->getNeeded($section);
        $exists = $this->getExists($section, $this->filePrefix);
        if ($renew) {
            $create = $needed;
        } else {
            $create = array_diff_key($needed, $exists);
        }
        $delete = array_diff_key($exists, $needed);
        array_walk($create, fn(string $name) => $this->createAdminPage($name, $section));
        array_walk($delete, [$this, 'deleteAdminPage']);
    }

    /**
     * Удаление файла из админки.
     *
     * @param string $name Имя файла без пути
     */
    private function deleteAdminPage(string $name): void
    {
        unlink($name);
    }

    /**
     * @throws FileNotFoundException
     */
    public function cleanAdminPages(): void
    {
        $exists = $this->getExists('admin/', $this->filePrefix);
        array_walk($exists, [$this, 'deleteAdminPage']);
    }

    /**
     * Добавление файла страницы админки.
     *
     * @param string $name    Имя файла без пути
     * @param string $section Раздел
     */
    private function createAdminPage(string $name, string $section): void
    {
        file_put_contents(
            $this->documentRoot . '/bitrix/admin/' . $this->filePrefix . $name,
            '<?php require($_SERVER["DOCUMENT_ROOT"]."' . $this->path . $section . $name . '");',
        );
    }

    /**
     * Проверка установленных файлов.
     *
     * @prop string $section наименование раздела для копирования
     * @prop string $filter часть имени файла по которому производится отбор
     *
     * @return array<string,string>
     *
     * @throws FileNotFoundException
     */
    private function getExists(string $section, string $filter = ''): array
    {
        $directory = new Directory(realpath($this->documentRoot . '/bitrix/' . $section));
        if (!$directory->isExists()) {
            return [];
        }
        $files = $directory->getChildren();
        $result = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $filename = $file->getName();
                if ('' === $filter || str_contains($filename, $filter)) {
                    $result[$filename] = $file->getPhysicalPath();
                }
            }
        }

        return $result;
    }

    /**
     * @prop string $section наименование раздела для копирования, как правило, это
     * - admin - страницы панели управления
     * - js - для файлов скриптов
     * - и т.п.
     *
     * @return array<string,string>
     *
     * @throws FileNotFoundException
     */
    public function getNeeded(string $section): array
    {
        $directory = new Directory(realpath($this->documentRoot . $this->path . $section));
        if (!$directory->isExists()) {
            return [];
        }
        $files = $directory->getChildren();
        $result = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $name = $file->getName();
                $result[$this->filePrefix . $name] = $name;
            }
        }

        return $result;
    }
}

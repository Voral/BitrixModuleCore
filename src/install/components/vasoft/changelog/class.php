<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

// @noinspection AutoloadingIssuesInspection

use Bitrix\Main\Context;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Loader;
use Vasoft\Core\Changelog\ChangelogSection;
use Vasoft\Core\Changelog\Markdown;
use Vasoft\Core\Changelog\ChangelogEntry;
use Vasoft\Core\Exceptions\FileException;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

/**
 * @property array $arParams
 */
class VasoftChangelogComponent extends CBitrixComponent implements Controllerable, Errorable
{
    private string $templatePage = 'template';

    protected ErrorCollection $errorCollection;
    private mixed $dateFormat = 'd.m.Y';

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @param array $arParams
     */
    public function onPrepareComponentParams($arParams): array
    {
        $this->errorCollection = new ErrorCollection();
        $arParams['FILE'] ??= '';
        $arParams['FILTER'] ??= '';
        $arParams['VERSION_REGEXP'] ??= '';
        $arParams['SECTION_REGEXP'] ??= '';
        $arParams['VERSION_COUNT'] = (int) ($arParams['VERSION_COUNT'] ?? 10);

        return $arParams;
    }

    protected function listKeysSignedParameters(): array
    {
        return [
            'FILE',
            'FILTER',
            'VERSION_REGEXP',
            'SECTION_REGEXP',
            'VERSION_COUNT',
        ];
    }

    /**
     * @throws LoaderException
     */
    public function executeComponent(): bool
    {
        global $APPLICATION;
        if ($APPLICATION->GetGroupRight('vasoft.core') < 'R') {
            return false;
        }
        if (!file_exists($this->arParams['FILE'])) {
            return false;
        }

        if (!$this->checkModules()) {
            return false;
        }
        $this->includeComponentTemplate($this->templatePage);

        return true;
    }

    /**
     * @throws LoaderException
     */
    private function checkModules(): bool
    {
        return Loader::includeModule('vasoft.core');
    }

    public function configureActions(): array
    {
        return [
            'list' => [],
        ];
    }

    /**
     * @return array|Error[]
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    /**
     * @param string $code
     *
     * @return null|Error
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    public function listAction(string $filter = ''): array
    {
        $filter = trim(strip_tags($filter));
        $result = [
            'list' => [],
            'find' => $filter,
        ];
        $languageId = Context::getCurrent()->getLanguage();
        $this->dateFormat = CDatabase::dateFormatToPhp(CSite::GetDateFormat($languageId));

        try {
            $this->checkModules();
            $parser = new Markdown($this->arParams['VERSION_REGEXP'], $this->arParams['SECTION_REGEXP']);
            $changelog = $parser->parseFromFile($this->arParams['FILE'], filter: $filter);
            $result['list'] = array_map($this->mapChangelogEntry(...), $changelog);
        } catch (FileException|LoaderException $e) {
            $this->errorCollection[] = new Error($e->getMessage());

            return [];
        }

        return $result;
    }

    private function mapChangelogEntry(ChangelogEntry $entry): array
    {
        return [
            'date' => $entry->date->format($this->dateFormat),
            'version' => $entry->version,
            'current' => $entry->last,
            'sections' => array_map($this->mapChangelogSection(...), $entry->sections),
        ];
    }

    private function mapChangelogSection(ChangelogSection $section): array
    {
        return [
            'name' => $section->title,
            'items' => $section->items,
        ];
    }
}

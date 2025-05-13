<?php

declare(strict_types=1);

namespace Vasoft\Core\Changelog;

use Bitrix\Main\Localization\Loc;
use Vasoft\Core\Exceptions\FileException;

class Markdown
{
    public const VERSION_REGEXP = '/^#\s*v?(?<version>[0-9\.]+)\s*\(?(?<year>[0-9]{4})-(?<month>[0-9]{2})-(?<day>[0-9]{2})\)?/';
    public const SECTION_REGEXP = '/^###\s*(?<section>[^#\n]+)\s*$/';

    private readonly string $versionRegexp;

    private readonly string $sectionRegexp;

    /**
     * @param string $versionRegexp Регулярное выражение для парсинга версий
     * @param string $sectionRegexp Регулярное выражение для парсинга секций
     */
    public function __construct(
        string $versionRegexp = '',
        string $sectionRegexp = '',
    ) {
        $this->versionRegexp = $versionRegexp ?: self::VERSION_REGEXP;
        $this->sectionRegexp = $sectionRegexp ?: self::SECTION_REGEXP;
    }

    /**
     * @param string $filePath Петь к файлу
     * @param int    $limit    количество версий в результатах
     * @param string $filter   фильтр поиска (можно по номеру версии или по дате)
     *
     * @return array<ChangelogEntry>
     *
     * @throws FileException
     */
    public function parseFromFile(string $filePath, int $limit = 10, string $filter = ''): array
    {
        if (!is_readable($filePath)) {
            throw new FileException(Loc::getMessage('VS_CORE_ERROR_FILE_NOT_READABLE', ['#FILE#' => $filePath]));
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new FileException(Loc::getMessage('VS_CORE_ERROR_FILE_OPEN', ['#FILE#' => $filePath]));
        }
        $changelog = [];
        $currentVersion = null;
        $currentSection = null;
        $count = 0;
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if (preg_match($this->versionRegexp, $line, $matches)) {
                if (null !== $currentVersion) {
                    if (null !== $currentSection) {
                        $currentVersion->sections[] = $currentSection;
                    }
                    if ('' === $filter || $this->matchFilter($currentVersion, $filter)) {
                        $changelog[] = $currentVersion;
                        if ($limit > 0 && ++$count >= $limit) {
                            $currentVersion = null;
                            $currentSection = null;
                            break;
                        }
                    }
                }
                $validDate = new \DateTimeImmutable(
                    sprintf('%04d-%02d-%02d', $matches['year'], $matches['month'], $matches['day']),
                );
                $currentVersion = new ChangelogEntry($matches[1], $validDate);
                $currentSection = null;

                continue;
            }

            if (preg_match($this->sectionRegexp, $line, $matches)) {
                if (null !== $currentSection) {
                    $currentVersion->sections[] = $currentSection;
                }
                $currentSection = new ChangelogSection(trim($matches[1]));

                continue;
            }
            if (null !== $currentSection && null !== $currentVersion) {
                if (preg_match('/^\s*[*-]\s+(.+)$/', $line, $matches)) {
                    $currentSection->items[] = $matches[1];

                    continue;
                }
                if (!empty($line) && !empty($currentSection->items)) {
                    $currentSection->items[count($currentSection->items) - 1] .= ' ' . $line;
                }
            }
        }

        if (null !== $currentVersion) {
            if (null !== $currentSection) {
                $currentVersion->sections[] = $currentSection;
            }
            if ('' === $filter || $this->matchFilter($currentVersion, $filter)) {
                $changelog[] = $currentVersion;
            }
        }

        return $changelog;
    }

    private function matchFilter(ChangelogEntry $entry, string $filter): bool
    {
        if ($entry->version === $filter) {
            return true;
        }

        if ($entry->date->format('Y-m-d') === $filter) {
            return true;
        }

        foreach ($entry->sections as $section) {
            foreach ($section->items as $item) {
                if ($this->isSimilar($item, $filter)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isSimilar(string $text, string $filter): bool
    {
        $text = strtolower($text);
        $filter = strtolower($filter);
        $words = explode(' ', $filter);
        foreach ($words as $word) {
            $word = trim($word);
            if ('' !== $word && !str_contains($text, $word)) {
                return false;
            }
        }

        return true;
    }
}

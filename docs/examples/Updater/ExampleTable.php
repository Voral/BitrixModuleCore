<?php

declare(strict_types=1);

namespace Vendor\Example\Updater;

use Bitrix\Main\Entity;

class ExampleTable extends Entity\DataManager
{
    /** @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getTableName()
    {
        return 'vasoft_core_example';
    }

    /**
     * @return array
     *
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getMap()
    {
        return [
            (new Entity\IntegerField('ID'))
                ->configureAutocomplete()
                ->configurePrimary(),
            new Entity\StringField('NAME'),
        ];
    }
}

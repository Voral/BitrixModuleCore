<?php

namespace Vasoft\Core\Updater\Example;

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
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getMap()
    {
        return array(
            (new Entity\IntegerField('ID'))
                ->configureAutocomplete()
                ->configurePrimary(),
            new Entity\StringField('NAME'),
        );
    }
}

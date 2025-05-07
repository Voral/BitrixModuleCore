<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\GroupTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class UserGroupsField extends SelectField
{
    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function __construct(string $code, string $description, \Closure $getter)
    {
        parent::__construct($code, $description, $getter);
        $this->configureGroups();
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function configureGroups(): void
    {
        $collection = GroupTable::query()
            ->addOrder('C_SORT')
            ->addSelect('ID')
            ->addSelect('NAME')
            ->fetchCollection();
        $this->options[0] = Loc::getMessage('FIELD_USER_GROUP_EMPTY');
        if ($collection instanceof \Iterator) {
            foreach ($collection as $group) {
                $this->options[$group->getId()] = $group->getName();
            }
        }
    }
}

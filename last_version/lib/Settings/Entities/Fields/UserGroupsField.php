<?php
namespace Vasoft\Core\Settings\Entities\Fields;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\GroupTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Closure;

class UserGroupsField extends SelectField
{
    /**
     * @param string $code
     * @param string $description
     * @param Closure $getter
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function __construct(string $code, string $description, Closure $getter)
    {
        parent::__construct($code, $description, $getter);
        $this->configureGroups();
    }

    /**
     * @return void
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
        foreach ($collection as $group) {
            $this->options[$group->getId()] = $group->getName();
        }
    }
}
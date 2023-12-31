<?php
namespace Vasoft\Core\Settings\Entities\Fields;

use Bitrix\Main\Localization\Loc;
use Vasoft\Core\Settings\Field;

class BooleanField extends Field
{
    public function renderInput(): string
    {
        $value = ($this->getter)();
        $checkedYes = $value === 'Y' ? ' checked' : '';
        $checkedNo = $value !== 'Y' ? ' checked' : '';
        $yesLabel = Loc::getMessage('BOOL_FIELD_YES');
        $noLabel = Loc::getMessage('BOOL_FIELD_NO');
        return <<<HTML
<input type="radio" value="Y" name="$this->code"$checkedYes> $yesLabel&nbsp;&nbsp; 
<input type="radio" value="N" name="$this->code"$checkedNo> $noLabel 
HTML;
    }
}
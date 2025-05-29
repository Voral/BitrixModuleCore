<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
/*
 * @global CMain $APPLICATION
 *
 * @var array $arParams
 * @var array $arResult
 * @var VasoftChangelogComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

$containerId = 'changelog' . uniqid();

?>
<div id="<?php
echo $containerId; ?>" class="vs-changelog adm-workarea">
</div>
<script type="text/javascript">
    BX.ready(function () {
        BX.VasoftChangelog.init({
            signedParameters: '<?php echo $this->getComponent()->getSignedParameters(); ?>',
            componentName: '<?php echo $this->getComponent()->getName(); ?>',
            containerId: '<?php echo $containerId; ?>',
            button: {
                title: '<?php echo Loc::getMessage('VS_CHANGELOG_FIND'); ?>',
                classes: 'adm-btn'
            },

        });
    })
</script>

<?php

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
echo $containerId; ?>" class="vs-changelog">
</div>
<script type="text/javascript">
    BX.ready(function () {
        BX.VasoftChangelog.init({
            signedParameters: '<?php echo $this->getComponent()->getSignedParameters(); ?>',
            componentName: '<?php echo $this->getComponent()->getName(); ?>',
            containerId: '<?php echo $containerId; ?>',
            button: {
                title: 'Find',
                classes: 'btn btn-success'
            },

        });
    })
</script>

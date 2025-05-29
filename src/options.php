<?php
/** @noinspection PhpUnhandledExceptionInspection
 * @noinspection PhpMultipleClassDeclarationsInspection
 *
 * @var CAllMain $APPLICATION
 *
 * @global $REQUEST_METHOD
 * @global $REQUEST_METHOD
 *
 * @var $RestoreDefaults
 * @var $Update
 * @var $mid
 */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Vasoft\Core\Settings\Entities\Fields;
use Vasoft\Core\Settings\Entities\Tab;
use Vasoft\Core\Settings\Render\Controller;

Loc::loadMessages(__FILE__);
$request = Main\Context::getCurrent()->getRequest();

$module_id = 'vasoft.core';
$rights = CMain::GetGroupRight($module_id);
if ($rights < 'R') {
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}
Main\Loader::includeModule($module_id);
$tabGeneral = new Tab(
    'info',
    Loc::getMessage('TAB_INFO'),
    [
        new Fields\HtmlField(static fn() => Loc::getMessage('README')),
    ],
);

$controller = new Controller(
    [
        $tabGeneral,
    ],
    true,
);
$tabControl = $controller->startTabControl('tabControl');
?>
    <form method="post"
          action="<?php
          echo $APPLICATION->GetCurPage(); ?>?mid=<?php
          echo htmlspecialcharsbx($mid); ?>&lang=<?php
          echo LANGUAGE_ID; ?>">
        <?php
        $controller->echoTabs($tabControl);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';
$tabControl->Buttons(); ?>
        <script>
            function RestoreDefaults() {
                if (confirm('<?php echo addslashes(Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING')); ?>'))
                    window.location = "<?php echo $APPLICATION->GetCurPage(
                    ); ?>?restore=Y&lang=<?php echo LANGUAGE_ID; ?>&mid=<?php echo urlencode(
                        $mid,
                    ); ?>&<?php echo bitrix_sessid_get(); ?>";
            }
        </script>
        <input <?php
        if ($rights < 'W') {
            echo 'disabled';
        } ?> type="submit" name="Update" value="<?php
        echo Loc::getMessage('MAIN_SAVE'); ?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?php
        echo Loc::getMessage('MAIN_RESET'); ?>">
        <input <?php
        if ($rights < 'W') {
            echo 'disabled';
        } ?> type="button"
             title="<?php
             echo Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS'); ?>"
             OnClick="RestoreDefaults();"
             value="<?php
             echo Loc::getMessage('VASOFT_CORE_DEFAULTS'); ?>">
    </form>
<?php
$tabControl->End();

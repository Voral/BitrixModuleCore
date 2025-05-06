<?php
/**
 * @bxnolanginspection
 *
 * @noinspection PhpUnhandledExceptionInspection
 *
 * @global  CMain $APPLICATION
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
use Vasoft\Core\Settings\Example\ExampleModuleSettings;
use Vasoft\Core\Settings\Example\ExampleOptions;
use Vasoft\Core\Settings\Render\Controller;

Loc::loadMessages(__FILE__);
$request = Main\Context::getCurrent()->getRequest();

$module_id = 'vendor.module';
$rights = CMain::GetGroupRight($module_id);
if ($rights < 'R') {
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}
Main\Loader::includeModule($module_id);
/**
 * Обновление кеша при каждом заходе на страницу лишь для примера,
 * В реальном модуле рекомендую кеш сбрасывать вручную или сделать кнопку
 * Он необходим если настройки могут быть установлены из консольных скриптов.
 */
$config = ExampleModuleSettings::getInstance(false);

$tabText = new Tab(
    'texts',
    'Тексты',
    [
        new Fields\HtmlField(static fn() => '<b>HTML</b> блок'),
        new Fields\NoteField(static fn() => '<b>HTML</b> блок стилизованный'),
    ],
);
/** @noinspection PhpUnhandledExceptionInspection */
$tabGeneral = new Tab(
    'general',
    Loc::getMessage('TAB_EXAMPLES_TITLE'),
    [
        (new Fields\NotZeroIntField(
            ExampleModuleSettings::PROP_EXAMPLE_INT,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_INT),
            $config->getExampleInt(...),
        ))->configureWidth(70),
        new Fields\TextField(
            ExampleModuleSettings::PROP_EXAMPLE_STRING,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_STRING),
            $config->getExampleString(...),
        ),
        new Fields\SeparatorField(),
        (new Fields\TextAreaField(
            ExampleModuleSettings::PROP_EXAMPLE_TEXT,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_TEXT),
            $config->getExampleText(...),
        ))
            ->configureHeight(100),
        (new Fields\SelectField(
            ExampleModuleSettings::PROP_EXAMPLE_SELECT,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_SELECT),
            $config->getExampleSelect(...),
        ))
            ->configureOptions(ExampleOptions::getList()),
        (new Fields\TextAreaField(
            ExampleModuleSettings::PROP_EXAMPLE_ARRAY,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_ARRAY),
            $config->getExampleArray(...),
        ))
            ->configureHeight(100)
            ->configureWidth(300),
        new Fields\BooleanField(
            ExampleModuleSettings::PROP_EXAMPLE_BOOL,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_BOOL),
            $config->getExampleBoolean(...),
        ),
        new Fields\UserGroupsField(
            ExampleModuleSettings::PROP_EXAMPLE_USER_GROUP,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_USER_GROUP),
            $config->getExampleUserGroupId(...),
        ),
    ],
);
// Обработка запроса
if ($request->isPost()) {
    try {
        if ($rights < 'W') {
            throw new Main\AccessDeniedException();
        }
        if (!check_bitrix_sessid()) {
            throw new Main\ArgumentException('Bad sessid.');
        }
        $config->saveFromArray($request->getPostList()->getValues());
    } catch (Exception $exception) {
        CAdminMessage::ShowMessage($exception->getMessage());
    }
} elseif (null !== $request->get('restore') && check_bitrix_sessid()) {
    $config->clean();
    $v1 = 'id';
    $v2 = 'asc';
    $z = CGroup::GetList($v1, $v2, ['ACTIVE' => 'Y', 'ADMIN' => 'N']);
    while ($zr = $z->Fetch()) {
        CMain::DelGroupRight($module_id, [$zr['ID']]);
    }
}
$a = Loc::getMessage('ADMIN_BUTTON_SAVE');

$controller = new Controller(
    [
        $tabText,
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

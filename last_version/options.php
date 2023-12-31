<?php /** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @var $APPLICATION CAllMain */
/** @global $REQUEST_METHOD */
/** @global $REQUEST_METHOD */
/** @var $RestoreDefaults */
/** @var $Update */

/** @var $mid */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Vasoft\Core\Settings\Entities\Fields;
use Vasoft\Core\Settings\Entities\Tab;
use Vasoft\Core\Settings\Example\ExampleModuleSettings;
use Vasoft\Core\Settings\Example\ExampleOptions;

Loc::loadMessages(__FILE__);
$request = Main\Context::getCurrent()->getRequest();

$module_id = "vasoft.core";
$rights = CMain::GetGroupRight($module_id);
if ($rights < "R") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
Main\Loader::includeModule($module_id);
/**
 * Обновление кеша при каждом заходе на страницу лишь для примера,
 * В реальном модуле рекомендую кеш сбрасывать вручную или сделать кнопку
 * Он необходим если настройки могут быть установлены из консольных скриптов
 */
$config = ExampleModuleSettings::getInstance(false);

$tabText = new Tab(
    'texts',
    Loc::getMessage('TAB_TEXTS_SHORTS'),
    [
        new Fields\HtmlField(static fn() => Loc::getMessage('README')),
        new Fields\NoteField(static fn() => Loc::getMessage('DEVELOPMENT_NOTE')),
    ]
);
$tabSettings = new Tab(
    'settings',
    Loc::getMessage('TAB_SETTINGS'),
    [
        new Fields\HtmlField(static fn() => Loc::getMessage('TEXT_SETTINGS')),
    ]
);
$tabUpdater = new Tab(
    'updater',
    Loc::getMessage('TAB_UPDATER'),
    [
        new Fields\HtmlField(static fn() => Loc::getMessage('TEXT_UPDATER')),
        new Fields\NoteField(static fn() => Loc::getMessage('NOTE_UPDATER')),
    ]
);
$tabUpdate = new Tab(
    'update',
    Loc::getMessage('TAB_TEXTS_SHORTS'),
    [
        new Fields\HtmlField(static fn() => Loc::getMessage('TEXT_SETTINGS')),
    ]
);
/** @noinspection PhpUnhandledExceptionInspection */
$tabGeneral = new Tab(
    'general',
    Loc::getMessage('TAB_EXAMPLES_TITLE'),
    [
        (new Fields\NotZeroIntField(
            ExampleModuleSettings::PROP_EXAMPLE_INT,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_INT),
            $config->getExampleInt(...)
        ))->configureWidth(70),
        new Fields\TextField(
            ExampleModuleSettings::PROP_EXAMPLE_STRING,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_STRING),
            $config->getExampleString(...)
        ),
        new Fields\SeparatorField(),
        (new Fields\TextAreaField(
            ExampleModuleSettings::PROP_EXAMPLE_TEXT,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_TEXT),
            $config->getExampleText(...)
        ))
            ->configureHeight(100),
        (new Fields\SelectField(
            ExampleModuleSettings::PROP_EXAMPLE_SELECT,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_SELECT),
            $config->getExampleSelect(...)
        ))
            ->configureOptions(ExampleOptions::getList()),
        (new Fields\TextAreaField(
            ExampleModuleSettings::PROP_EXAMPLE_ARRAY,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_ARRAY),
            $config->getExampleArray(...)
        ))
            ->configureHeight(100)
            ->configureWidth(300),
        (new Fields\BooleanField(
            ExampleModuleSettings::PROP_EXAMPLE_BOOL,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_BOOL),
            $config->getExampleBoolean(...)
        )),
        (new Fields\UserGroupsField(
            ExampleModuleSettings::PROP_EXAMPLE_USER_GROUP,
            $config->getOptionName(ExampleModuleSettings::PROP_EXAMPLE_USER_GROUP),
            $config->getExampleUserGroupId(...)
        )),
    ]
);
// Обработка запроса
if ($request->isPost()) {
    try {
        if ($rights < "W") {
            throw new Main\AccessDeniedException();
        }
        if (!check_bitrix_sessid()) {
            throw new Main\ArgumentException("Bad sessid.");
        }
        $config->saveFromArray($request->getPostList()->getValues());
    } catch (Exception $exception) {
        CAdminMessage::ShowMessage($exception->getMessage());
    }
} elseif ($request->get("restore") !== null && check_bitrix_sessid()) {
    $config->clean();
    $v1 = "id";
    $v2 = "asc";
    $z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
    while ($zr = $z->Fetch()) {
        CMain::DelGroupRight($module_id, array($zr["ID"]));
    }
}
$a = Loc::getMessage('ADMIN_BUTTON_SAVE');

$controller = new \Vasoft\Core\Settings\Render\Controller(
    [
        $tabText,
        $tabSettings,
        $tabUpdater,
        $tabGeneral
    ],
    true
);
$tabControl = $controller->startTabControl('tabControl');
?>
    <form method="post"
          action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
        <?php
        $controller->echoTabs($tabControl);
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
        $tabControl->Buttons(); ?>
        <script>
            function RestoreDefaults() {
                if (confirm('<?= AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                    window.location = "<?= $APPLICATION->GetCurPage()?>?restore=Y&lang=<?=LANGUAGE_ID?>&mid=<?= urlencode($mid)?>&<?=bitrix_sessid_get()?>";
            }
        </script>
        <input <?php if ($rights < "W") {
            echo "disabled";
        } ?> type="submit" name="Update" value="<?=Loc::getMessage("MAIN_SAVE")?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?=Loc::getMessage('MAIN_RESET')?>">
        <input <?php if ($rights < "W") {
            echo "disabled";
        } ?> type="button"
             title="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
             OnClick="RestoreDefaults();"
             value="<?= Loc::getMessage("VASOFT_CORE_DEFAULTS") ?>">
    </form>
<?php
$tabControl->End();

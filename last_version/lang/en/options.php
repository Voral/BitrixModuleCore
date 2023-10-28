<?php
$MESS['TAB_TEXTS_SHORTS'] = 'Texts';
$MESS['TAB_SETTINGS'] = 'Module Settings';
$MESS['TAB_UPDATER'] = 'Update Tools';
$MESS['TAB_EXAMPLES_TITLE'] = 'Field Examples';
$MESS['README'] = '<p>This module is intended for solution developers. It contains useful classes and tools.</p>';
$MESS['DEVELOPMENT_NOTE'] = <<<HTML
<p>When using the module, especially for creating configuration classes, it is recommended to include this module in your module's include.php. To prevent issues in case the 'vasoft.core' module is removed, it's advisable to subscribe to the 'onBeforeRemoveVasoftCore' module event. The handler should pass the module name and module ID as data. Here's an example of a handler (Vasoft\Core\Updater\Example\DependencyHandler):</p>
<code style="unicode-bidi: embed;font-family: monospace;white-space: pre;">namespace Vasoft\Core\Updater\Example;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class DependencyHandler
{
    /**
     * Preventing the removal of the 'vasoft.core' module
     * @param Event \$event
     * @return EventResult
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    public static function onBeforeRemoveVasoftCore(Event \$event): EventResult
    {
        /**
         * Return the module name as the second parameter
         * And the module identifier as the third
         */
        return new EventResult(EventResult::ERROR, 'Example module name', 'vendor.module');
    }
}
</code>
HTML;
$MESS['TEXT_SETTINGS'] = <<<HTML
<p>To manage module settings, the following classes described below can be useful. Explanations are provided in comments in the code itself.</p>
<h2>Module Settings</h2>
<p><strong>\Vasoft\Core\Settings\ModuleSettings</strong></p>
<p>An abstract class for creating module settings classes. Implements the singleton pattern. Classes built on this can be used in modules to retrieve specific setting values.</p>
<h2>Base Field Class</h2>
<p><strong>\Vasoft\Core\Settings\Field</strong></p>
<p>An abstract class for creating your own classes to represent various data types on module settings pages.</p>
<p>In this module, a number of classes have been implemented and can be used. They are located in \Vasoft\Core\Settings\Entities\Fields.</p>
<h2>Interface for Enumerating List Options for Select Lists</h2>
<p><strong>\Vasoft\Core\Settings\SelectOptionsInterface</strong></p>
<h2>Trait for Use in Enumerations for Select Lists</h2>
<p><strong>\Vasoft\Core\Settings\SelectOptions</strong></p>
<h2>Usage Examples</h2>
<p>This page is generated for the settings of \Vasoft\Core\Settings\Example\ExampleModuleSettings.</p>
<p>You can find some solutions in the directory /bitrix/modules/vasoft.core/lib/Settings/Example/. Also, the configuration of the settings page itself can be found in the file /bitrix/modules/vasoft.core/options.php.</p>
<p>Examples of using existing field display implementations can be found on the "Field Examples" tab.</p>
HTML;

$MESS['TEXT_UPDATER'] = <<<HTML
<p>Helper classes for module updates, installations, and transfers</p>
<h2>FileInstaller</h2>
<p>Creates and removes necessary administrative section pages based on the source directory's contents. There is also a method for removing created pages.</p>
<h2>HandlerInstaller</h2>
<p>Registers and removes event handlers as per the specified list. There is also a method for removing created handlers.</p>
<h2>TableInstaller</h2>
<p>Initializes necessary tables based on the provided list. There is also a method for removing created tables.</p>
<h2>OptionsDump</h2>
<p>Tool for importing/exporting module settings.</p>
HTML;
$MESS['NOTE_UPDATER'] = <<<HTML
<p>Please note that if your web server is configured in a way that the path includes a reference rather than a real path, it's better to specify the real path in the caching settings as the SID, rather than following the examples in the documentation. For example:</p>
<code style="unicode-bidi: embed;font-family: monospace;white-space: pre;">return array(
    'cache' => array(
        'value' => array(
            'type' => 'memcache',
            'memcache' => array(
                'host' => 'unix:///var/test/memcached.sock',
                'port' => '0',
            ),
            'sid' => realpath(\$_SERVER['DOCUMENT_ROOT'])."#memcached",
        ),
    ),
 );
</code>
<p>Alternatively, it's generally a better practice to create a static cache identifier that won't clash with other Bitrix installations on the same server.</p>
<p>If you don't do this in a command-line script and for the web, cache identifiers will be different, and the cache won't automatically clear when changes are made.</p>
<h2>Example</h2>
<p>You can find usage examples in the script /bitrix/modules/vasoft.core/lib/Updater/Example/cli-example.php</p>
HTML;

$MESS['VASOFT_CORE_DEFAULTS'] = 'Default';
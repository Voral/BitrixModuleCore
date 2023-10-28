<?php
$MESS['TAB_TEXTS_SHORTS'] = 'Тексты';
$MESS['TAB_SETTINGS'] = 'Настройки модулей';
$MESS['TAB_UPDATER'] = 'Инструменты обновления';
$MESS['TAB_EXAMPLES_TITLE'] = 'Примеры полей';
$MESS['README'] = '<p>Модуль предназначен для разработчиков. Содержит полезные классы и инструменты</p>';
$MESS['DEVELOPMENT_NOTE'] = <<<HTML
<p>При использовании модуля, в частности для создания классов конфигурации, данный модуль желательно подключать в include.php своего модуля. При этом во избежание ситуации, когда модуль vasoft.core удалили - необходимо подписываться на событие модуля onBeforeRemoveVasoftCore. Обработчик должен передать в качестве данных наименование модуля и идентификатор модуля. Пример  обработчика (Vasoft\Core\Updater\Example\DependencyHandler):</p>
<code style="unicode-bidi: embed;font-family: monospace;white-space: pre;">namespace Vasoft\Core\Updater\Example;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class DependencyHandler
{
    /**
     * Предотвращение удаления модуля vasoft.core
     * @param Event \$event
     * @return EventResult
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    public static function onBeforeRemoveVasoftCore(Event \$event): EventResult
    {
        /**
         * Возвращаем наименование модуля вторым параметром
         * И идентификатор модуля третьим
         */
        return new EventResult(EventResult::ERROR, 'Example module name', 'vendor.module');
    }
}
</code>
HTML;
$MESS['TEXT_SETTINGS'] = <<<HTML
<p>Для управления настройками модулей могут быть полезны ниже описанные классы. Пояснения в комментариях в самом коде</p>
<h2>Настройки модуля</h2>
<p><strong>\Vasoft\Core\Settings\ModuleSettings</strong></p>
<p>Абстрактный класс для создания классов настроек модулей. Реализует паттерн одиночка. Построенные на нем классы можно использовать в модулях для получения значений конкретный настроек</p>
<h2>Базовый класс полей</h2>
<p><strong>\Vasoft\Core\Settings\Field</strong></p>
<p>Абстрактный класс для создания своих классов представления различных типов данных на страницах настройки модулей</p>
<p>В данном модуле реализован ряд классов, которые можно использовать. Они расположены в \Vasoft\Core\Settings\Entities\Fields</p>
<h2>Интерфейс для перечисления списка опций для отображения списков выбора</h2>
<p><strong>\Vasoft\Core\Settings\SelectOptionsInterface</strong></p>
<h2>Трейт для использования в перечислениях для списков выбора</h2>
<p><strong>\Vasoft\Core\Settings\SelectOptions</strong></p>
<p>Для реализации опций списков выбора. Перечисление при этом должно реализовывать интерфейс SelectOptionsInterface</p>
<h2>Примеры использования</h2>
<p>Данная страничка формируется для настроек \Vasoft\Core\Settings\Example\ExampleModuleSettings</p>
<p>Можно посмотреть некоторые решения в каталоге /bitrix/modules/vasoft.core/lib/Settings/Example/. А так же конфигурировании самой страницы настроек в файле /bitrix/modules/vasoft.core/options.php</p>
<p>Примеры использования существующих реализаций отображения полей - на вкладке "Примеры полей"</p>
HTML;


$MESS['TEXT_UPDATER'] = <<<HTML
<p>Вспомогательные классы для обновления, установки и переноса модулей</p>
<h2>FileInstaller</h2>
<p>Создает необходимые и удаляет устаревшие страницы административной части в соответствии с содержимым директории источника. Так же есть метод для удаления созданных</p>
<h2>HandlerInstaller</h2>
<p>Регистрирует необходимые и удаляет устаревшие обработчики событий в соответствии с заданным списком. Так же есть метод для удаления созданных</p>
<h2>TableInstaller</h2>
<p>Инициализирует необходимые таблицы в соответствии с заданным списком. Так же есть метлд для удаления созданных</p>
<h2>OptionsDump</h2>
<p>Инструмент для импорта/экспорта настроек модулей</p>
HTML;
$MESS['NOTE_UPDATER'] = <<<HTML
<p>Обратите внимание, если у вас веб сервер настроен так что указан не реальный путь, а в пути есть ссылка, то в настройках
кеширования в качестве sid лучше указать реальный путь, а не так как указано в примерах документации, например так:</p>
<code style="unicode-bidi: embed;    font-family: monospace;    white-space: pre;">return array(
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
<p>Или, что в общем случае будет более правильным, создать статический идентификатор кеша, чтобы он не пересекался с другими установками Bitrix на том же сервере</p>
<p>Если этого не сделать в консольном скрипте и для веба идентификаторы кеша окажутся разными и кеш не сбросится автоматически при изменениях.</p>
<h2>Пример</h2>
<p>Примеры использования можно посмотреть в скрипте /bitrix/modules/vasoft.core/lib/Updater/Example/cli-example.php</p>
HTML;
$MESS['VASOFT_CORE_DEFAULTS'] = 'По умолчанию';

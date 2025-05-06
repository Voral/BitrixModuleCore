# Подключение модуля

Модуль предназначен для разработчиков. Содержит вспомогательные классы и инструменты, которыми можно воспользоваться при
создании своих модулей.

При использовании модуля, в частности для создания классов конфигурации, данный модуль желательно подключать в include.php своего модуля. При этом во избежание ситуации, когда модуль vasoft.core удалили - необходимо подписываться на событие модуля onBeforeRemoveVasoftCore. Обработчик должен передать в качестве данных наименование модуля и идентификатор модуля. [Пример обработчика](examples/Updater/DependencyHandler.php)

```php
namespace Vendor\Module\Updater;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class DependencyHandler
{
    /**
    * Предотвращение удаления модуля vasoft.core
    * @param Event \$event
    * @return EventResult
    */
    public static function onBeforeRemoveVasoftCore(Event \$event): EventResult
    {
        /**
        * Возвращаем наименование модуля вторым параметром
        * и идентификатор модуля третьим
        */
        return new EventResult(EventResult::ERROR, 'Example module name', 'vendor.module');
    }
}
```

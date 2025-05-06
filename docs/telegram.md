# Отправка сообщений в телеграм

Для отправки сообщений в телеграм можно воспользоваться соответствующим сервисом

```php
if (\Bitrix\Main\Loader::includeModule('vasoft.core')) {
    $sender = new \Vasoft\Core\Notify\Sender\Telegram('your_token','your_chat_id'); 
    $sender->send('Single message');
    $sender->send([
        'Line 1 of multiline message',
        'Line 2 of multiline message',
    ]);
}
```
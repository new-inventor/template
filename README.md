#Template

Класс для замены заглушек мекстом.
Границы заглушек определяются в настройках ``$config['template']['borders']``.
пример конфига можно посмотреть в файле src/config/default.

## Использование
```
$template = new Template($someString)

$placeholders = $template->getPlaceholders();
foreach($placeholders as $placeholder){
    do something ..
    $template->addReplacement($resString);
}

return $template->getReplaced();
```

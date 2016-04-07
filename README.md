#Template

Класс для замены заглушек мекстом.
Границы заглушек определяются в настройках.

##Использование

```
$template = new Template($someString)

$placeholders = $template->getPlaceholders();
foreach($placeholders as $placeholder){
    do something ..
    $template->addReplacement($resString);
}

return $template->getReplaced();
```
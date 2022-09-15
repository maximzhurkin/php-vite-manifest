# ViteManifest
Класс для подключения ресурсов (js/css) из сгенерированного manifest.json бандлера Vite

## Как использовать
```php
// Создать экземпляр класса
$vm = new ViteManifest();
// Подключить файл манифеста
$vm->loadManifest('/path/to/you/manifest.json');
// Можно загрузить правила определения точек входа методом loadRules
$vm->loadRules('/path/to/you/manifest.rules.php');
// Если нужно установить префикс для всех ресурсов, по умолчанию пусто
$vm->setBaseUrl('/');
// Получаем все ресурсы манифеста
// Если был использован метод loadRules
$assets = $vm->getCurrentAssets();
// Или получить ресурсы конкретной точки входа
$assets = $vm->getAssets('entry.html');
// Метод вернет примерный массив:
// [
//   'module' => [
//     'modern' => '/assets/js/entry-e6c79cd1.js',
//     'legacy' => '/assets/js/entry-legacy-65636e94.js',
//   ],
//   'preloads' => [
//     '/assets/js/header-07304c08.js',
//     '/assets/js/footer-20e37c84.js',
//   ],
//   'styles' => [
//     '/assets/css/header-bedf54c7.css',
//     '/assets/css/footer-3056f931.css',
//     '/assets/css/entry-bf688ca1.css',
//   ],
//   'polyfill' => '/assets/polyfills-legacy.1b45e6f2.js',
// ];
// Или можно получить конкретные ресурсы используя методы:
$module = $vm->getModule($entry);
$moduleLegacy = $vm->getModuleLegacy($entry);
$imports = $vm->getPreloadModules($entry);
$styles = $vm->getStyles($entry);
$polyfill = $vm->getPolyfill();
```

## Конфиг
Структура ключ => значение, где ключ регулярное выражение, а значение точка входа из файла манифеста. Важен порядок обработки регулярок, пример файла конфигурации:
```php
<?php

return [
  '/entities/([0-9]+)/childrens/([0-9]+)/' => 'children',
  '/entities/([0-9]+)/' => 'entity',
  '/entities/' => 'entities',
];
```

## Разработка
```sh
php -S localhost:8000
```
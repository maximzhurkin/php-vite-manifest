<?php

/** 
 * Класс для подключения ресурсов (js/css) 
 * из сгенерированного manifest.json бандлера Vite 
 * [https://vitejs.dev]
 * 
 * Copyright (c) 2022 Max Zhurkin <maximzhurkin@gmail.com>
 * 
 * MIT License [http://www.opensource.org/licenses/mit-license.php]
 */
class ViteManifest
{

  /**
   * Текущий URI
   * @var string
   */
  protected $uri;

  /**
   * Правила соотношений URI с точками входа из манифеста
   * @var array
   */
  protected $rules;

  /**
   * Распарсенный manifest.json в массив
   * @var array
   */
  protected $manifest;

  /**
   * Префикс к ресурсам
   * 
   * @var string
   */
  protected $baseUrl;


  function __construct()
  {
    // Устанавливаем текущий урл
    $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  }

  /**
   * Получить полный путь с префиксом baseUrl
   * 
   * @param string $path
   * 
   * @return string
   */
  private function getUrl($url)
  {
    return $this->baseUrl . $url;
  }

  /**
   * Получить данные из точки входа по ключу
   * 
   * @param string $entry
   * @param string $key
   * 
   * @return array
   */
  private function getEntryData($entry, $key)
  {
    return isset($this->manifest[$entry][$key])
      ? $this->manifest[$entry][$key]
      : [];
  }

  /**
   * Установить префикс к ресурсам
   * 
   * @param string $baseUrl
   * 
   * @return void
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }

  /**
   * Добавить правило
   * 
   * @param string $rule
   * @param string $entry
   * 
   * @return void
   */
  public function addRule($rule, $entry)
  {
    $this->rules[$rule] = $entry;
  }

  /**
   * Загрузить manifest.json
   * 
   * @param string $file
   * 
   * @return void
   */
  public function loadManifest($file)
  {
    if (file_exists($file)) {
      $this->manifest = json_decode(file_get_contents($file), true);
    } else {
      throw new \Exception('Manifest json file not found');
    }
  }

  /**
   * Загрузить правила из файла
   * 
   * @param string $file
   * 
   * @return void
   */
  public function loadRules($file)
  {
    if (file_exists($file)) {
      $rules = include($file);

      foreach ($rules as $rule => $entry) {
        $this->addRule($rule, $entry);
      }
    } else {
      throw new \Exception('Config rules file not found');
    }
  }

  /**
   * Получить имя точки входа из манифеста по текущему урлу
   * 
   * @return string
   */
  public function getCurrentEntry()
  {
    foreach ($this->rules as $rule => $entry) {
      if (preg_match("~^{$rule}$~", $this->uri)) {
        return $entry;
      }
    }

    return false;
  }

  /**
   * Получить имя точки входа для старых браузеров
   * 
   * @param string $entry
   * 
   * @return string
   */
  public function getLegacyEntry($entry)
  {
    $extension = pathinfo($entry, PATHINFO_EXTENSION);

    return basename($entry, ".$extension") . '-legacy.' . $extension;
  }

  /**
   * Получить точку входа из манифеста
   * 
   * @param string $entry
   * 
   * @return string
   */
  public function getModule($entry)
  {
    return isset($this->manifest[$entry]['file'])
      ? $this->getUrl($this->manifest[$entry]['file'])
      : '';
  }

  /**
   * Получить polyfills-legacy.js
   * 
   * @return string
   */
  public function getPolyfill()
  {
    foreach ($this->manifest as $entry => $values) {
      if (preg_match("~^(.*)legacy-polyfills$~", $entry)) {
        return isset($values['file']) 
          ? $this->getUrl($values['file'])
          : false;
      }
    }

    return false;
  }
  
  /**
   * Получить точку входа для старых браузеров
   * 
   * @param string $entry
   * 
   * @return string
   */
  public function getModuleLegacy($entry)
  {
    return $this->getModule($this->getLegacyEntry($entry));
  }

  /**
   * Получить массив импортов точки входа
   * 
   * @param string $entry
   * 
   * @return array
   */
  public function getImports($entry)
  {
    return $this->getEntryData($entry, 'imports');
  }

  /**
   * Получить css точки входа
   * @param mixed $entry
   * 
   * @return [type]
   */
  public function getCss($entry)
  {
    return $this->getEntryData($entry, 'css');
  }

  /**
   * Получить дочерние модули точки входа
   * 
   * @param string $entry
   * 
   * @return array
   */
  public function getPreloadModules($entry)
  {
    $modules = [];

    foreach ($this->getImports($entry) as $module) {
      $modules[] = $this->getModule($module);
    }

    return $modules;
  }
  
  /**
   * Получить все стили с дочерними модулями
   * 
   * @param string $entry
   * 
   * @return array
   */
  public function getStyles($entry)
  {
    $styles = [];
    $modules = $this->getImports($entry);

    // Получаем стили дочерних модулей
    foreach ($modules as $module) {
      foreach ($this->getCss($module) as $style) {
        $styles[] = $this->getUrl($style);
      }
    }
    // Получаем стили точки входа
    foreach ($this->getCss($entry) as $style) {
      $styles[] = $this->getUrl($style);
    }

    return $styles;
  }

  /**
   * Получить все ресурсы
   * 
   * @param string $entry
   * 
   * @return array
   */
  public function getAssets($entry)
  {
    return [
      'module' => [
        'modern' => $this->getModule($entry),
        'legacy' => $this->getModuleLegacy($entry),
      ],
      'preloads' => $this->getPreloadModules($entry),
      'styles' => $this->getStyles($entry),
      'polyfill' => $this->getPolyfill(),
    ];
  }

  /**
   * Получить все ресурсы по текущей точке входа
   * 
   * @return array
   */
  public function getCurrentAssets()
  {
    return $this->getAssets($this->getCurrentEntry());
  }

}

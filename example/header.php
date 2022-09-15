<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/../src/ViteManifest.php');

$vm = new ViteManifest();
$vm->loadRules($_SERVER['DOCUMENT_ROOT'] . '/manifest.rules.php');
$vm->loadManifest($_SERVER['DOCUMENT_ROOT'] . '/assets/manifest.json');
$vm->setBaseUrl('/');

$assets = $vm->getCurrentAssets();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manifest demo</title>
  <?php if (isset($assets['module']['modern'])) : ?>
    <script type="module" crossorigin src="<?= $assets['module']['modern'] ?>"></script>
  <?php endif ?>
  <?php if (isset($assets['preloads'])) : ?>
    <?php foreach ($assets['preloads'] as $preload) : ?>
      <link rel="modulepreload" href="<?= $preload ?>">
    <?php endforeach ?>
  <?php endif ?>
  <?php if (isset($assets['styles'])) : ?>
    <?php foreach ($assets['styles'] as $style) : ?>
      <link rel="stylesheet" href="<?= $style ?>">
    <?php endforeach ?>
  <?php endif ?>
  <script type="module">
    try {
      import("_").catch(() => 1);
    } catch (e) {}
    window.__vite_is_dynamic_import_support = true;
  </script>
  <script type="module">
    ! function() {
      if (window.__vite_is_dynamic_import_support) return;
      console.warn("vite: loading legacy build because dynamic import is unsupported, syntax error above should be ignored");
      var e = document.getElementById("vite-legacy-polyfill"),
        n = document.createElement("script");
      n.src = e.src, n.onload = function() {
        System.import(document.getElementById('vite-legacy-entry').getAttribute('data-src'))
      }, document.body.appendChild(n)
    }();
  </script>
</head>

<body>
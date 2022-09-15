<?php

$links = [
  '/entity/',
  '/entity/458/',
  '/entity/458/children/890/',
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manifest demo</title>
</head>

<body>
  <h1>Manifest demo</h1>
  <ul>
    <?php foreach ($links as $link) : ?>
      <li>
        <a href="<?= $link ?>"><?= $link ?></a>
      </li>
    <?php endforeach ?>
  </ul>
  <p>
    <a href="/assets/manifest.json">/example/assets/manifest.json</a>
  </p>
</body>

</html>
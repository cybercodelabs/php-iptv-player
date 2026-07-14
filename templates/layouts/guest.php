<?php
/** @var string $content */
/** @var string $appName */
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#e50914">
    <title><?= e(($title ?? 'Iniciar sesión') . ' · ' . $appName) ?></title>
    <link rel="icon" href="<?= e(asset('img/favicon.ico')) ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= e(asset('css/login/variables.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/background.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/card.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/logo.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/form.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/input.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/button.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/alert.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/modal.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/login/mobile.css')) ?>">
</head>
<body>
<?= $content ?>
<script src="<?= e(asset('js/login/form.js')) ?>"></script>
</body>
</html>

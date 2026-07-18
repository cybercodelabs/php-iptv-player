<?php
/** @var string $content */
/** @var string $appName */
/** @var string $title */
/** @var list<string> $styles */
/** @var list<string> $scripts */
/** @var list<string> $cdnStyles */
/** @var list<string> $cdnScripts */
/** @var string $appBackground */
/** @var bool $showAtmosphere */
$styles = $styles ?? [];
$scripts = $scripts ?? [];
$cdnStyles = $cdnStyles ?? [];
$cdnScripts = $cdnScripts ?? [];
$appBackground = $appBackground ?? \App\Infrastructure\Config\Config::homeBackground();
$showAtmosphere = $showAtmosphere ?? true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e50914">
    <title><?= e(($title ?? '') . ' · ' . $appName) ?></title>
    <link rel="icon" href="<?= e(asset('img/favicon.ico')) ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <?php if ($showAtmosphere): ?>
        <link rel="stylesheet" href="<?= e(asset('css/layout/atmosphere.css')) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= e(asset('css/layout/header.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/layout/footer.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/search/modal.css')) ?>">
    <?php foreach ($cdnStyles as $cdnStyle): ?>
        <link rel="stylesheet" href="<?= e($cdnStyle) ?>">
    <?php endforeach; ?>
    <?php foreach ($styles as $style): ?>
        <link rel="stylesheet" href="<?= e(asset($style)) ?>">
    <?php endforeach; ?>
</head>
<body class="app-body<?= $showAtmosphere ? '' : ' app-body--no-atmosphere' ?>">
<?php if ($showAtmosphere): ?>
    <div class="app-atmosphere app-atmosphere--<?= e($appBackground) ?>" aria-hidden="true"></div>
<?php endif; ?>
<?php require templates_path('partials/header.php'); ?>

<main class="app-main">
    <?= $content ?>
</main>

<?php require templates_path('partials/search-modal.php'); ?>
<?php require templates_path('partials/footer.php'); ?>

<script src="<?= e(asset('js/search/modal.js')) ?>"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php foreach ($cdnScripts as $cdnScript): ?>
    <script src="<?= e($cdnScript) ?>"></script>
<?php endforeach; ?>
<?php foreach ($scripts as $script): ?>
    <script src="<?= e(asset($script)) ?>"></script>
<?php endforeach; ?>
</body>
</html>

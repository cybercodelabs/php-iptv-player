<?php
/** @var string $content */
/** @var string $appName */
/** @var string $title */
/** @var list<string> $styles */
/** @var list<string> $scripts */
$styles = $styles ?? [];
$scripts = $scripts ?? [];
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
    <link rel="stylesheet" href="<?= e(asset('css/layout/header.css')) ?>">
    <?php foreach ($styles as $style): ?>
        <link rel="stylesheet" href="<?= e(asset($style)) ?>">
    <?php endforeach; ?>
</head>
<body class="app-body">
<?php require templates_path('partials/header.php'); ?>

<main class="app-main">
    <?= $content ?>
</main>

<?php require templates_path('partials/footer.php'); ?>

<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php foreach ($scripts as $script): ?>
    <script src="<?= e(asset($script)) ?>"></script>
<?php endforeach; ?>
</body>
</html>

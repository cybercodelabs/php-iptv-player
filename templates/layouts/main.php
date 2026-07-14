<?php
/** @var string $content */
/** @var string $appName */
/** @var string $title */
$username = \App\Infrastructure\Session\Session::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? '') . ' · ' . $appName) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="app-body">
<?php require templates_path('partials/header.php'); ?>

<main class="container">
    <?= $content ?>
</main>

<?php require templates_path('partials/footer.php'); ?>

<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>

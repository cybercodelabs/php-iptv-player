<?php
/** @var string|null $username */
/** @var array<string, mixed> $userInfo */
?>
<section>
    <h1 class="h3 mb-3">Mi cuenta</h1>
    <dl class="row">
        <dt class="col-sm-3">Usuario</dt>
        <dd class="col-sm-9"><?= e($username ?? '') ?></dd>
        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9"><?= e((string) ($userInfo['status'] ?? '—')) ?></dd>
        <dt class="col-sm-3">Expira</dt>
        <dd class="col-sm-9">
            <?php
            $exp = $userInfo['exp_date'] ?? null;
            echo e($exp ? date('Y-m-d H:i', (int) $exp) : '—');
            ?>
        </dd>
    </dl>
    <p class="mt-4">
        <a class="btn btn-outline-light btn-sm" href="<?= e(url('logout')) ?>">Cerrar sesión</a>
    </p>
</section>

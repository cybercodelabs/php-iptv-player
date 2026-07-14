<section>
    <h1 class="h3">Hola, <?= e($username ?? '') ?></h1>
    <p class="text-secondary">Dashboard del esqueleto. Los catálogos se conectarán a Xtream en las siguientes iteraciones.</p>
    <div class="row" style="margin-top: 1.25rem;">
        <a class="card-feature" href="<?= e(url('channels')) ?>">
            <h2>TV en vivo</h2>
            <p>Canales y categorías</p>
        </a>
        <a class="card-feature" href="<?= e(url('movies')) ?>">
            <h2>Películas</h2>
            <p>Catálogo VOD</p>
        </a>
        <a class="card-feature" href="<?= e(url('series')) ?>">
            <h2>Series</h2>
            <p>Temporadas y episodios</p>
        </a>
    </div>
</section>

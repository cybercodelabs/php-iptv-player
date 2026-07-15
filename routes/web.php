<?php

declare(strict_types=1);

use App\Features\Auth\Presentation\LoginController;
use App\Features\Auth\Presentation\LogoutController;
use App\Features\Dashboard\Presentation\BackgroundsController;
use App\Features\Dashboard\Presentation\HomeController;
use App\Features\Live\Presentation\ChannelController;
use App\Features\Live\Presentation\ChannelsController;
use App\Features\Movies\Presentation\MovieController;
use App\Features\Movies\Presentation\MoviesController;
use App\Features\Profile\Presentation\ProfileController;
use App\Features\Series\Presentation\EpisodeController;
use App\Features\Series\Presentation\SerieController;
use App\Features\Series\Presentation\SeriesController;
use App\Features\Search\Presentation\SearchCatalogController;
use App\Infrastructure\Http\Router;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Session\Session;

/** @var Router $router */
$router = new Router();

$router->get('/', static function (): void {
    Response::redirect(Session::isAuthenticated() ? 'home' : 'login');
});

$login = new LoginController();
$router->get('/login', [$login, 'show']);
$router->post('/login', [$login, 'attempt']);
$router->get('/logout', new LogoutController());

$router->get('/home', new HomeController());
$router->get('/backgrounds', new BackgroundsController());
$router->get('/channels', new ChannelsController());
$router->get('/channel', new ChannelController());
$router->get('/movies', new MoviesController());
$router->get('/movie', new MovieController());
$router->get('/series', new SeriesController());
$router->get('/serie', new SerieController());
$router->get('/episode', new EpisodeController());
$router->get('/profile', new ProfileController());
$router->get('/api/search/catalog', new SearchCatalogController());

return $router;

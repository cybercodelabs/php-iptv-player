<?php

declare(strict_types=1);

namespace App\Features\Auth\Presentation;

use App\Infrastructure\Config\Config;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;
use Throwable;

/**
 * Login y autenticación contra Xtream UI.
 */
final class LoginController
{
    public function show(): void
    {
        AuthMiddleware::guestOnly();

        View::render('auth/login', [
            'title' => 'Iniciar sesión',
            'error' => Session::flash('error'),
            'errorTitle' => Session::flash('error_title') ?? 'Ocurrió un error',
            'appName' => Config::appName(),
        ], 'layouts/guest');
    }

    public function attempt(): void
    {
        AuthMiddleware::guestOnly();

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->fail('Datos incompletos', 'Usuario y contraseña son obligatorios.');
        }

        try {
            $client = new XtreamClient(Config::xtreamHost());
            $data = $client->authenticate($username, $password);
            $userInfo = $data['user_info'] ?? null;

            if (!is_array($userInfo) || (int) ($userInfo['auth'] ?? 0) !== 1) {
                $this->fail(
                    'Credenciales incorrectas',
                    'El usuario o la contraseña no son válidos.'
                );
            }

            $status = strtolower((string) ($userInfo['status'] ?? ''));
            if ($status === 'banned') {
                $this->fail(
                    'Cuenta no disponible',
                    'Tu cuenta está bloqueada. Contacta al administrador del servicio.'
                );
            }

            $expDate = $userInfo['exp_date'] ?? null;
            if ($expDate !== null && $expDate !== '' && (int) $expDate > 0 && time() > (int) $expDate) {
                $this->fail(
                    'Suscripción vencida',
                    'Tu plan ha expirado. Renueva tu suscripción para continuar.'
                );
            }

            Session::login([
                'username' => $username,
                'password' => $password,
                'user_info' => $userInfo,
                'server_info' => is_array($data['server_info'] ?? null) ? $data['server_info'] : [],
            ]);

            Response::redirect('home');
        } catch (Throwable $e) {
            $isUnavailable = (int) $e->getCode() === 503;
            $title = $isUnavailable ? 'Sin conexión' : 'No se pudo iniciar sesión';
            $message = $isUnavailable
                ? 'No pudimos contactar el servidor IPTV en este momento.'
                : 'Inténtalo de nuevo en unos segundos.';

            if (Config::get('APP_DEBUG') === 'true') {
                $message = $e->getMessage();
            }

            $this->fail($title, $message);
        }
    }

    private function fail(string $title, string $message): never
    {
        Session::flash('error_title', $title);
        Session::flash('error', $message);
        Response::redirect('login');
    }
}

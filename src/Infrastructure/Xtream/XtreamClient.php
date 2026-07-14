<?php

declare(strict_types=1);

namespace App\Infrastructure\Xtream;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Cliente HTTP hacia la API player_api.php de Xtream UI.
 * Esqueleto: implementar acciones en siguientes iteraciones.
 */
final class XtreamClient
{
    private Client $http;
    private string $host;

    public function __construct(string $host, ?Client $http = null)
    {
        $this->host = rtrim($host, '/');
        $this->http = $http ?? new Client([
            'timeout' => 15,
            'http_errors' => false,
            'verify' => true,
        ]);
    }

    /**
     * Autentica y obtiene información de cuenta (sin action).
     *
     * @return array<string, mixed>
     */
    public function authenticate(string $username, string $password): array
    {
        return $this->request($username, $password);
    }

    /**
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function request(string $username, string $password, array $query = []): array
    {
        if ($this->host === '') {
            throw new RuntimeException('XTREAM_HOST no está configurado.');
        }

        $params = array_merge([
            'username' => $username,
            'password' => $password,
        ], $query);

        $url = $this->host . '/player_api.php?' . http_build_query($params);

        try {
            $response = $this->http->get($url);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Error al contactar el servidor Xtream: ' . $e->getMessage(), 0, $e);
        }

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new RuntimeException('Respuesta inválida del servidor Xtream.');
        }

        return $data;
    }

    public function streamUrl(string $type, string $username, string $password, string|int $id, string $extension = 'm3u8'): string
    {
        return sprintf(
            '%s/%s/%s/%s/%s.%s',
            $this->host,
            $type,
            rawurlencode($username),
            rawurlencode($password),
            $id,
            ltrim($extension, '.')
        );
    }
}

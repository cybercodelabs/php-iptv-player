<?php

declare(strict_types=1);

namespace App\Infrastructure\Xtream;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Cliente HTTP hacia la API player_api.php de Xtream UI.
 */
final class XtreamClient
{
    private Client $http;
    private string $host;

    public function __construct(string $host, ?Client $http = null)
    {
        $this->host = rtrim($host, '/');
        $this->http = $http ?? new Client([
            'timeout' => 45,
            'http_errors' => false,
            'verify' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function authenticate(string $username, string $password): array
    {
        $data = $this->request($username, $password);

        // Login: vacío o sin user_info = credenciales inválidas
        if ($data === [] || !isset($data['user_info']) || !is_array($data['user_info'])) {
            return [
                'user_info' => [
                    'auth' => 0,
                ],
            ];
        }

        return $data;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getVodStreams(string $username, string $password, ?int $categoryId = null): array
    {
        $query = ['action' => 'get_vod_streams'];
        if ($categoryId !== null) {
            $query['category_id'] = $categoryId;
        }

        return $this->listRequest($username, $password, $query);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getSeries(string $username, string $password, ?int $categoryId = null): array
    {
        $query = ['action' => 'get_series'];
        if ($categoryId !== null) {
            $query['category_id'] = $categoryId;
        }

        return $this->listRequest($username, $password, $query);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getLiveStreams(string $username, string $password, ?int $categoryId = null): array
    {
        $query = ['action' => 'get_live_streams'];
        if ($categoryId !== null) {
            $query['category_id'] = $categoryId;
        }

        return $this->listRequest($username, $password, $query);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getLiveCategories(string $username, string $password): array
    {
        return $this->listRequest($username, $password, [
            'action' => 'get_live_categories',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getVodCategories(string $username, string $password): array
    {
        return $this->listRequest($username, $password, [
            'action' => 'get_vod_categories',
        ]);
    }

    /**
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>|list<mixed>
     */
    public function request(string $username, string $password, array $query = []): array
    {
        if ($this->host === '') {
            throw new RuntimeException('XTREAM_HOST no está configurado.', 503);
        }

        $params = array_merge([
            'username' => $username,
            'password' => $password,
        ], $query);

        $url = $this->host . '/player_api.php?' . http_build_query($params);

        try {
            $response = $this->http->get($url);
        } catch (GuzzleException $e) {
            throw new RuntimeException('No se pudo contactar el servidor IPTV.', 503, $e);
        }

        $status = $response->getStatusCode();
        if ($status >= 500) {
            throw new RuntimeException('El servidor IPTV no está disponible.', 503);
        }

        if ($status === 401 || $status === 403) {
            return [];
        }

        $body = trim((string) $response->getBody());
        if ($body === '') {
            return [];
        }

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param array<string, scalar|null> $query
     * @return list<array<string, mixed>>
     */
    private function listRequest(string $username, string $password, array $query): array
    {
        $data = $this->request($username, $password, $query);

        if ($data === []) {
            return [];
        }

        // Algunos paneles envuelven el listado; la mayoría devuelve un array indexado
        if ($this->isList($data)) {
            /** @var list<array<string, mixed>> $list */
            $list = array_values(array_filter($data, 'is_array'));

            return $list;
        }

        return [];
    }

    /**
     * @param array<mixed> $data
     */
    private function isList(array $data): bool
    {
        if ($data === []) {
            return true;
        }

        return array_keys($data) === range(0, count($data) - 1);
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

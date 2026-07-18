<?php

declare(strict_types=1);

namespace App\Features\Profile\Application;

/**
 * Normaliza datos de sesión Xtream para la página de perfil.
 */
final class ProfileMapper
{
    /**
     * @param array<string, mixed> $userInfo
     * @param array<string, mixed> $serverInfo
     * @return array{
     *   username: string,
     *   status: string,
     *   statusLabel: string,
     *   isActive: bool,
     *   expDate: string|null,
     *   createdAt: string|null,
     *   maxConnections: string,
     *   activeConnections: string,
     *   isTrial: bool,
     *   formats: list<string>,
     *   serverTimezone: string|null,
     *   serverUrl: string|null
     * }
     */
    public function map(string $username, array $userInfo, array $serverInfo = []): array
    {
        $statusRaw = strtolower(trim((string) ($userInfo['status'] ?? 'unknown')));
        $isActive = $statusRaw === 'active';

        $formats = [];
        $allowed = $userInfo['allowed_output_formats'] ?? [];
        if (is_array($allowed)) {
            foreach ($allowed as $format) {
                $value = trim((string) $format);
                if ($value !== '') {
                    $formats[] = strtoupper($value);
                }
            }
        }

        return [
            'username' => $username,
            'status' => $statusRaw !== '' ? $statusRaw : 'unknown',
            'statusLabel' => $this->statusLabel($statusRaw),
            'isActive' => $isActive,
            'expDate' => $this->formatTimestamp($userInfo['exp_date'] ?? null),
            'createdAt' => $this->formatTimestamp($userInfo['created_at'] ?? null),
            'maxConnections' => $this->stringOrDash($userInfo['max_connections'] ?? null),
            'activeConnections' => $this->stringOrDash($userInfo['active_cons'] ?? null),
            'isTrial' => ((string) ($userInfo['is_trial'] ?? '0')) === '1',
            'formats' => $formats,
            'serverTimezone' => $this->nullableString($serverInfo['timezone'] ?? null),
            'serverUrl' => $this->buildServerUrl($serverInfo),
        ];
    }

    /**
     * @param array<string, mixed> $serverInfo
     */
    private function buildServerUrl(array $serverInfo): ?string
    {
        $host = $this->nullableString($serverInfo['url'] ?? null);
        if ($host === null) {
            return null;
        }

        $protocol = strtolower(trim((string) ($serverInfo['server_protocol'] ?? 'http')));
        if ($protocol !== 'https' && $protocol !== 'http') {
            $protocol = 'http';
        }

        $port = trim((string) ($serverInfo['port'] ?? ''));
        $defaultPort = $protocol === 'https' ? '443' : '80';
        $suffix = ($port !== '' && $port !== $defaultPort) ? ':' . $port : '';

        return $protocol . '://' . $host . $suffix;
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Cuenta activa',
            'banned' => 'Cuenta bloqueada',
            'disabled' => 'Cuenta deshabilitada',
            'expired' => 'Suscripción vencida',
            default => 'Estado desconocido',
        };
    }

    private function formatTimestamp(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (is_numeric($value) && (int) $value > 0) {
            return date('d/m/Y H:i', (int) $value);
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $parsed = strtotime($text);

        return $parsed ? date('d/m/Y H:i', $parsed) : $text;
    }

    private function stringOrDash(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return (string) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }
}

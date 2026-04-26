<?php

declare(strict_types=1);

namespace App\Shared\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use UKFast\HealthCheck\Facade\HealthCheck;
use UKFast\HealthCheck\HealthCheck as HealthCheckContract;

class SystemHealthChecksWidget extends Widget
{
    protected string $view = 'filament.widgets.system-health-checks-widget';
    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'checks' => $this->resolveStatuses()->map(function (array $check): array {
                $status = $check['status'] ?? 'UNKNOWN';

                return [
                    ...$check,
                    'label' => $this->resolveCheckLabel($check['name']),
                    'status_label' => $this->resolveStatusLabel($status),
                    'badge_color' => $this->resolveBadgeColor($status),
                    'context_items' => $this->resolveContextItems($check['context']),
                ];
            }),
        ];
    }

    public function getHeading(): string
    {
        return 'Состояние системы';
    }

    /**
     * @return Collection<int, array{name: string, status: string|null, message: string, context: array<string, mixed>}>
     */
    private function resolveStatuses(): Collection
    {
        /** @var Collection<int, HealthCheckContract> $checks */
        $checks = HealthCheck::all();

        return $checks
            ->map(
                function (HealthCheckContract $check): array {
                    $status = $check->status();

                    return [
                        'name' => $check->name(),
                        'status' => $status->getStatus(),
                        'message' => $status->message(),
                        'context' => $status->context(),
                    ];
                }
            );
    }

    private function resolveCheckLabel(string $name): string
    {
        return match ($name) {
            'database' => 'База данных',
            'redis' => 'Redis',
            'cache' => 'Кэш',
            'storage' => 'Файловое хранилище',
            'migration' => 'Миграции',
            'env' => 'Окружение',
            'system_resources' => 'Ресурсы системы',
            default => str($name)->replace('_', ' ')->headline()->toString(),
        };
    }

    private function resolveStatusLabel(?string $status): string
    {
        return match ($status) {
            'OK' => 'Работает',
            'DEGRADED' => 'Требует внимания',
            'PROBLEM' => 'Ошибка',
            default => 'Неизвестно',
        };
    }

    private function resolveBadgeColor(?string $status): string
    {
        return match ($status) {
            'OK' => 'success',
            'DEGRADED' => 'warning',
            'PROBLEM' => 'danger',
            default => 'gray',
        };
    }

    /**
     * @param  array<string, mixed>  $context
     * @return list<array{label: string, value: string}>
     */
    private function resolveContextItems(array $context): array
    {
        $items = [];

        foreach ($context as $key => $value) {
            $items[] = [
                'label' => $this->resolveContextLabel($key),
                'value' => $this->stringifyContextValue($value),
            ];
        }

        return $items;
    }

    private function resolveContextLabel(string $key): string
    {
        return match ($key) {
            'media_disk' => 'Медиа-диск',
            'media_root' => 'Корневая директория',
            'disk_free_gb' => 'Свободно, ГБ',
            'disk_total_gb' => 'Всего, ГБ',
            'php_memory_usage_mb' => 'Использование PHP, МБ',
            'php_peak_memory_mb' => 'Пиковая память PHP, МБ',
            'php_memory_limit_mb' => 'Лимит памяти PHP, МБ',
            default => str($key)->replace('_', ' ')->headline()->toString(),
        };
    }

    private function stringifyContextValue(mixed $value): string
    {
        if (is_array($value)) {
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return is_string($json) ? $json : '-';
        }

        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '-';
    }
}

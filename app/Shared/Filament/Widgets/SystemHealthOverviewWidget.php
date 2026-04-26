<?php

declare(strict_types=1);

namespace App\Shared\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use UKFast\HealthCheck\Facade\HealthCheck;
use UKFast\HealthCheck\HealthCheck as HealthCheckContract;

class SystemHealthOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $statuses = $this->resolveStatuses();
        $checksCount = $statuses->count();
        $problemCount = $statuses->where('status', 'PROBLEM')->count();
        $degradedCount = $statuses->where('status', 'DEGRADED')->count();
        $okayCount = $statuses->where('status', 'OK')->count();
        $systemCheck = $statuses->firstWhere('name', 'system_resources');
        $systemCheck = is_array($systemCheck) ? $systemCheck : null;

        return [
            Stat::make('Проверки системы', sprintf('%d из %d', $okayCount, $checksCount))
                ->description('Ошибок: ' . $problemCount . ' • Предупреждений: ' . $degradedCount)
                ->color($problemCount > 0 ? 'danger' : ($degradedCount > 0 ? 'warning' : 'success')),
            Stat::make('Свободное место на диске', $this->contextString($systemCheck, 'disk_free_gb', '0') . ' ГБ')
                ->description('Диск хранения: ' . $this->contextString($systemCheck, 'media_disk', 'public'))
                ->color('primary'),
            Stat::make('Память PHP', $this->contextString($systemCheck, 'php_memory_usage_mb', '0') . ' МБ')
                ->description('Пиковое значение: ' . $this->contextString($systemCheck, 'php_peak_memory_mb', '0') . ' МБ')
                ->color('gray'),
        ];
    }

    /**
     * @return Collection<int, array{name: string, status: string|null, message: string, context: array<string, mixed>}>
     */
    private function resolveStatuses(): Collection
    {
        /** @var Collection<int, HealthCheckContract> $checks */
        $checks = HealthCheck::all();

        return $checks->map(function (HealthCheckContract $check): array {
            $status = $check->status();

            return [
                'name' => $check->name(),
                'status' => $status->getStatus(),
                'message' => $status->message(),
                'context' => $status->context(),
            ];
        });
    }

    protected function getHeading(): string
    {
        return 'Обзор состояния системы';
    }

    /**
     * @param  array{name: string, status: string|null, message: string, context: array<string, mixed>}|null  $status
     */
    private function contextString(?array $status, string $key, string $fallback): string
    {
        $value = $status['context'][$key] ?? null;

        return is_string($value) || is_int($value) || is_float($value)
            ? (string) $value
            : $fallback;
    }
}

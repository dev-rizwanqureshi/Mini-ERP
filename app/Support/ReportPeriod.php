<?php

namespace App\Support;

use Carbon\CarbonImmutable;

class ReportPeriod
{
    public static function fromFilters(array $filters): array
    {
        $period = in_array($filters['period'] ?? null, ['day', 'week', 'month', 'year', 'custom'], true)
            ? $filters['period']
            : 'month';

        return match ($period) {
            'day' => self::day($filters['period_value'] ?? null),
            'week' => self::week($filters['period_value'] ?? null),
            'year' => self::year($filters['period_value'] ?? null),
            'custom' => self::custom($filters['date_from'] ?? null, $filters['date_to'] ?? null),
            default => self::month($filters['period_value'] ?? null),
        };
    }

    private static function day(?string $value): array
    {
        $date = self::safeDate($value, now()->toDateString());

        return self::range('day', $date->startOfDay(), $date->endOfDay(), $date->format('M j, Y'));
    }

    private static function week(?string $value): array
    {
        if ($value && preg_match('/^(?<year>\d{4})-W(?<week>\d{2})$/', $value, $matches)) {
            $date = CarbonImmutable::now()->setISODate((int) $matches['year'], (int) $matches['week']);
        } else {
            $date = CarbonImmutable::now();
            $value = $date->format('o-\WW');
        }

        return self::range('week', $date->startOfWeek(), $date->endOfWeek(), 'Week of '.$date->startOfWeek()->format('M j, Y'), $value);
    }

    private static function month(?string $value): array
    {
        $date = self::safeDate(($value ?: now()->format('Y-m')).'-01', now()->startOfMonth()->toDateString());

        return self::range('month', $date->startOfMonth(), $date->endOfMonth(), $date->format('F Y'), $date->format('Y-m'));
    }

    private static function year(?string $value): array
    {
        $year = (int) ($value ?: now()->year);
        $year = $year > 1970 && $year < 3000 ? $year : now()->year;
        $date = CarbonImmutable::create($year, 1, 1);

        return self::range('year', $date->startOfYear(), $date->endOfYear(), (string) $year, (string) $year);
    }

    private static function custom(?string $from, ?string $to): array
    {
        $start = self::safeDate($from, now()->startOfMonth()->toDateString())->startOfDay();
        $end = self::safeDate($to, now()->toDateString())->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->startOfDay(), $start->endOfDay()];
        }

        return self::range('custom', $start, $end, $start->format('M j, Y').' - '.$end->format('M j, Y'));
    }

    private static function range(string $period, CarbonImmutable $start, CarbonImmutable $end, string $label, ?string $value = null): array
    {
        return [
            'period' => $period,
            'period_value' => $value ?? match ($period) {
                'day' => $start->toDateString(),
                'month' => $start->format('Y-m'),
                'year' => $start->format('Y'),
                default => null,
            },
            'label' => $label,
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
            'start' => $start,
            'end' => $end,
        ];
    }

    private static function safeDate(?string $value, string $fallback): CarbonImmutable
    {
        try {
            return CarbonImmutable::parse($value ?: $fallback);
        } catch (\Throwable) {
            return CarbonImmutable::parse($fallback);
        }
    }
}

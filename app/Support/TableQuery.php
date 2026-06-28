<?php

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TableQuery
{
    public static function applySort(Builder $query, Request|array $input, array $allowed, string $defaultSort = 'created_at', string $defaultDirection = 'desc'): Builder
    {
        $filters = $input instanceof Request ? $input->all() : $input;
        $sort = (string) ($filters['sort'] ?? $defaultSort);
        $direction = strtolower((string) ($filters['direction'] ?? $defaultDirection)) === 'asc' ? 'asc' : 'desc';
        $sorter = $allowed[$sort] ?? $allowed[$defaultSort] ?? null;

        if ($sorter instanceof Closure) {
            $sorter($query, $direction);

            return $query;
        }

        if (is_string($sorter)) {
            $query->orderBy($sorter, $direction);
        }

        return $query;
    }

    public static function filters(Request $request, array $keys): array
    {
        return collect($request->only($keys))
            ->filter(fn (mixed $value) => $value !== null && $value !== '')
            ->all();
    }
}

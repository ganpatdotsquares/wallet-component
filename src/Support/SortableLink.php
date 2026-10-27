<?php

namespace admin\wallets\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SortableLink
{
    public static function render(string $column, ?string $title = null, array $queryParams = [], array $options = []): string
    {
        $title = $title ?: Str::title(str_replace('_', ' ', $column));

        $currentSort      = request()->query('sort');
        $currentDirection = request()->query('direction', 'asc');
        $isActive         = $currentSort === $column;
        $nextDirection    = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';

        $query = array_merge(request()->query(), $queryParams, [
            'sort'      => $column,
            'direction' => $nextDirection,
        ]);

        $url = request()->url() . '?' . http_build_query($query);

        $class = trim('sortable-link ' . Arr::get($options, 'class', ''));
        $icon  = $isActive ? ($currentDirection === 'asc' ? ' ▲' : ' ▼') : '';

        return sprintf(
            '<a href="%s" class="%s">%s%s</a>',
            e($url),
            e($class),
            e($title),
            $icon
        );
    }
}

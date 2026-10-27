<?php

namespace admin\wallets\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSortable(Builder $query): Builder
    {
        $allowed = $this->sortable ?? [];
        $column  = request()->query('sort');
        $direction = strtolower((string) request()->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($column && in_array($column, $allowed, true)) {
            return $query->orderBy($column, $direction);
        }

        return $query;
    }
}

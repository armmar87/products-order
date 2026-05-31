<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter
{
    protected Builder $query;
    protected array $filters;

    public function __construct(Builder $query, array $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    /**
     * Apply all filters to the query.
     */
    public function apply(): Builder
    {
        foreach ($this->filters as $name => $value) {
            if (method_exists($this, $name) && !empty($value)) {
                $this->$name($value);
            }
        }

        return $this->query;
    }

    /**
     * Filter by search query (name or description).
     */
    protected function search(?string $value): void
    {
        if ($value) {
            $this->query->where(function($q) use ($value) {
                $q->where('name', 'like', "%{$value}%")
                  ->orWhere('slug', 'like', "%{$value}%")
                  ->orWhere('description', 'like', "%{$value}%");
            });
        }
    }
}


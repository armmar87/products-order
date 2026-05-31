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

    protected function search(?string $value): void
    {
        if ($value) {
            $this->query->whereFullText(['name', 'slug', 'description'], $value);
            $this->query->whereRaw(
                "MATCH(name, slug, description) AGAINST(? IN NATURAL LANGUAGE MODE)",
                [$value]
            );
        }
    }
}


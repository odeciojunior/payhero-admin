<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait PaginatableTrait
 * @package App\Traits
 */
trait PaginatableTrait
{
    protected $perPageMax = 100;

    /**
     * Get the number of models to return per page.
     * @return int
     */
    public function getPerPage(): int
    {
        /** @var Model $instance */
        $instance = $this;
        $perPage  = request('per_page', $instance->perPage);

        if ($perPage === 'all') {
            $perPage = $instance->newQuery()->count();
        }

        return max(1, min($this->perPageMax, (int) $perPage));
    }

    /**
     * @param int $perPageMax
     */
    public function setPerPageMax(int $perPageMax): void
    {
        $this->perPageMax = $perPageMax;
    }
}

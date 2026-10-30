<?php

namespace Modules\Hospital\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Hospital\Models\Category;

class CategoryRepository
{
    public function query(): Builder
    {
        return Category::query();
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function findByType(string $type, ?int $hospitalId = null): Collection
    {
        return Category::query()
            ->where('type', $type)
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('code', 'asc')
            ->get();
    }

    public function getActiveByType(string $type, ?int $hospitalId = null): Collection
    {
        return Category::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('name', 'asc')
            ->get();
    }

    public function generateNextCode(string $type, ?int $hospitalId = null): string
    {
        $maxCode = Category::query()
            ->where('type', $type)
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->selectRaw('COALESCE(MAX(CAST(code AS INTEGER)), 0) as max_code')
            ->value('max_code');

        return (string) (((int) $maxCode) + 1);
    }
}


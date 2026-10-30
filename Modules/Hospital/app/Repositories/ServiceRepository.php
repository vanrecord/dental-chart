<?php

namespace Modules\Hospital\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Hospital\Models\Service;

class ServiceRepository
{
    public function query(): Builder
    {
        return Service::query();
    }

    public function find(int $id): ?Service
    {
        return Service::find($id);
    }

    public function findByType(string $type, ?int $hospitalId = null): Collection
    {
        return Service::query()
            ->where('type', $type)
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('code', 'asc')
            ->get();
    }

    public function generateNextCode(string $type, ?int $hospitalId = null): string
    {
        $maxCode = Service::query()
            ->where('type', $type)
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->selectRaw('COALESCE(MAX(CAST(code AS INTEGER)), 0) as max_code')
            ->value('max_code');

        return (string) (((int) $maxCode) + 1);
    }
}


<?php

namespace Modules\ActivityLog\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log an action in the application.
     *
     * @param string $action
     * @param string $description
     * @param Model|null $subject
     * @param array|null $properties
     * @return void
     */
    public function log(string $action, string $description, ?Model $subject = null, ?array $properties = null): void
    {
        $payload = [
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('single')->info("[ActivityLog] {$action}: {$description}", $payload);
    }
}


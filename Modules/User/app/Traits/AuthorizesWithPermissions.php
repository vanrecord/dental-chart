<?php

namespace Modules\User\Traits;

use App\Models\User;

trait AuthorizesWithPermissions
{
    /**
     * Authorize action using full permission identifier: module:controller:function.
     * Enforces permission checks on the backend with 403 Forbidden on failure.
     */
    protected function checkPermission(string $permission): void
    {
        $user = auth()->user();

        abort_if(
            ! $user || ! $user->can($permission),
            403,
            "Unauthorized access. Required permission: [{$permission}]."
        );
    }
}


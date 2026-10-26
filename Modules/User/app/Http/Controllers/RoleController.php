<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\User\Http\Requests\RoleRequest;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Support\PermissionRegistry;
use Modules\User\Traits\AuthorizesWithPermissions;

class RoleController extends Controller
{
    use AuthorizesWithPermissions;

    /**
     * Standard action columns for display in the matrix.
     */
    protected array $standardActions = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'update' => 'Update',
        'delete' => 'Delete',
        'export' => 'Export',
        'print' => 'Print',
    ];

    /**
     * Display a listing of roles.
     */
    public function index(): View
    {
        $this->checkPermission('users:roles:view');

        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();

        return view('user::roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $this->checkPermission('users:roles:create');

        $groupedPermissions = $this->getGroupedPermissions();
        $actions = $this->standardActions;

        return view('user::roles.create', compact('groupedPermissions', 'actions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        $this->checkPermission('users:roles:create');

        $validated = $request->validated();

        $role = Role::create([
            'name' => strtolower(trim($validated['name'])),
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->label}' was created successfully.");
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        $this->checkPermission('users:roles:view');

        $role->load(['permissions', 'users']);
        $groupedPermissions = $this->getGroupedPermissions();
        $actions = $this->standardActions;

        return view('user::roles.show', compact('role', 'groupedPermissions', 'actions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $this->checkPermission('users:roles:edit');

        $role->load('permissions');
        $groupedPermissions = $this->getGroupedPermissions();
        $actions = $this->standardActions;

        return view('user::roles.edit', compact('role', 'groupedPermissions', 'actions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->checkPermission('users:roles:update');

        $validated = $request->validated();

        $updateData = [
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
        ];

        // Only allow changing name if not default admin or super-admin role
        if (! in_array($role->name, ['admin', 'super-admin'], true)) {
            $updateData['name'] = strtolower(trim($validated['name']));
        }

        $role->update($updateData);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->label}' was updated successfully.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->checkPermission('users:roles:delete');

        if (in_array($role->name, ['admin', 'super-admin'], true)) {
            return redirect()->route('roles.index')
                ->with('error', "The system '{$role->name}' role cannot be deleted.");
        }

        $label = $role->label;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role '{$label}' was deleted successfully.");
    }

    /**
     * Structure permissions grouped by module and controller.
     */
    protected function getGroupedPermissions(): array
    {
        $all = Permission::orderBy('module')->orderBy('controller')->orderBy('id')->get();

        $grouped = [];
        foreach ($all as $permission) {
            $moduleKey = $permission->module;
            $controllerKey = $permission->controller;

            if (! isset($grouped[$moduleKey])) {
                $grouped[$moduleKey] = [
                    'label' => PermissionRegistry::moduleLabel($moduleKey),
                    'controllers' => [],
                ];
            }

            if (! isset($grouped[$moduleKey]['controllers'][$controllerKey])) {
                $grouped[$moduleKey]['controllers'][$controllerKey] = [
                    'label' => PermissionRegistry::controllerLabel($controllerKey),
                    'permissions' => collect(),
                ];
            }

            $grouped[$moduleKey]['controllers'][$controllerKey]['permissions']->push($permission);
        }

        return $grouped;
    }
}


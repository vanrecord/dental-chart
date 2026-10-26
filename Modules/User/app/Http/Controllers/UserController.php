<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Models\Role;
use Modules\User\Traits\AuthorizesWithPermissions;

class UserController extends Controller
{
    use AuthorizesWithPermissions;

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $this->checkPermission('users:users:view');

        $query = User::with('roles');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->input('role_id')) {
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $roles = Role::orderBy('label')->get();

        return view('user::users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $this->checkPermission('users:users:create');

        $roles = Role::orderBy('label')->get();

        return view('user::users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $this->checkPermission('users:users:create');

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (! empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' was created successfully.");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $this->checkPermission('users:users:view');

        $user->load('roles.permissions');
        $effectivePermissions = $user->getAllPermissions()->groupBy('module');

        return view('user::users.show', compact('user', 'effectivePermissions'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->checkPermission('users:users:edit');

        $user->load('roles');
        $roles = Role::orderBy('label')->get();

        return view('user::users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->checkPermission('users:users:update');

        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' was updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->checkPermission('users:users:delete');

        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$name}' was deleted successfully.");
    }
}

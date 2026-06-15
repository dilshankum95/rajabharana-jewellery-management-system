<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffUserRequest;
use App\Http\Requests\Admin\UpdateStaffUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffUserController extends Controller
{
    public function index(): View
    {
        $users = User::staffAccounts()
            ->latest()
            ->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::assignableRoles(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => UserRole::assignableRoles(),
        ]);
    }

    public function store(StoreStaffUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone') ?? '0770000000',
            'address' => 'Staff account',
            'city' => 'Colombo',
            'role' => $request->validated('role'),
            'password' => Hash::make($request->validated('password')),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->role instanceof UserRole && $user->role->isManagedStaffAccount(), 404);

        return view('admin.users.edit', [
            'staffUser' => $user,
            'roles' => UserRole::assignableRoles(),
        ]);
    }

    public function update(UpdateStaffUserRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->role instanceof UserRole && $user->role->isManagedStaffAccount(), 404);

        $data = $request->safe()->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Staff account updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('users.manage'), 403);
        abort_unless($user->role instanceof UserRole && $user->role->isManagedStaffAccount(), 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->role === UserRole::Admin && User::where('role', UserRole::Admin)->count() <= 1) {
            return back()->with('error', 'Cannot delete the last administrator account.');
        }

        if ($user->profile_photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Staff account removed.');
    }
}

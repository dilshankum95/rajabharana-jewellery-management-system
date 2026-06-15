<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title text-xl">Staff Accounts</h1>
                <p class="jewel-page-subtitle">Manage role-based access for administrators and staff</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="jewel-btn">+ Create Account</a>
        </div>
    </x-slot>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><x-role-badge :role="$user->role" /></td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td class="text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline ml-3"
                                        onsubmit="return confirm('Remove this staff account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">No staff accounts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</x-admin-layout>

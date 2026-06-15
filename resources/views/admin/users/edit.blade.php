<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to staff accounts</a>
            <h1 class="jewel-page-title text-xl mt-1">Edit Staff Account</h1>
            <p class="jewel-page-subtitle">{{ $staffUser->email }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.users.update', $staffUser) }}" class="jewel-card jewel-card-body space-y-6">
            @csrf @method('PATCH')
            @include('admin.users._form', ['staffUser' => $staffUser, 'roles' => $roles])
            <button type="submit" class="jewel-btn">Save Changes</button>
        </form>
    </div>
</x-admin-layout>

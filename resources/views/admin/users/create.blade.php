<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to staff accounts</a>
            <h1 class="jewel-page-title text-xl mt-1">Create Staff Account</h1>
            <p class="jewel-page-subtitle">Assign a role with the appropriate system permissions</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.users.store') }}" class="jewel-card jewel-card-body space-y-6">
            @csrf
            @include('admin.users._form', ['roles' => $roles])
            <button type="submit" class="jewel-btn">Create Account</button>
        </form>
    </div>
</x-admin-layout>

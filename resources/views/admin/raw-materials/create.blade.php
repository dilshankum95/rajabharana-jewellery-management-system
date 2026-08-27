<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.raw-materials.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to raw materials</a>
            <h1 class="jewel-page-title text-xl mt-2">Add Raw Material</h1>
        </div>
    </x-slot>

    <div class="jewel-card p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.raw-materials.store') }}">
            @csrf
            @include('admin.raw-materials._form', compact('materialTypes', 'stockUnits'))
            <div class="mt-8 flex gap-3">
                <button type="submit" class="jewel-btn">Save Material</button>
                <a href="{{ route('admin.raw-materials.index') }}" class="jewel-btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>

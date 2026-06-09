<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.catalog.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to catalog</a>
            <h1 class="jewel-page-title text-xl mt-1">Add Catalog Item</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.catalog.store') }}" enctype="multipart/form-data"
            class="jewel-card jewel-card-body space-y-6">
            @csrf
            @include('admin.catalog._form', compact('categories', 'goldQualities', 'availabilityStatuses'))
            <button type="submit" class="jewel-btn">Create Item</button>
        </form>
    </div>
</x-admin-layout>

<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.catalog.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to catalog</a>
            <h1 class="jewel-page-title text-xl mt-1">Edit: {{ $design->name }}</h1>
            <p class="jewel-page-subtitle font-mono text-xs">{{ $design->code }}</p>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        @if($errors->any())
            <x-alert type="error" class="mb-6">
                <p class="font-semibold">Please fix the following errors:</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('admin.catalog.update', $design) }}" enctype="multipart/form-data"
            class="jewel-card jewel-card-body space-y-6">
            @csrf @method('PATCH')
            @include('admin.catalog._form', [
                'design' => $design,
                'categories' => $categories,
                'goldQualities' => $goldQualities,
                'availabilityStatuses' => $availabilityStatuses,
                'rawMaterials' => $rawMaterials,
            ])
            <button type="submit" class="jewel-btn">Save Changes</button>
        </form>

        @include('admin.catalog._images', ['design' => $design])
    </div>
</x-admin-layout>

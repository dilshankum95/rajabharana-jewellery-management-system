<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCatalogDesignRequest;
use App\Http\Requests\Admin\UpdateCatalogDesignRequest;
use App\Models\CatalogDesign;
use App\Models\CatalogImage;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\FilterCatalogDesignRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CatalogDesignController extends Controller
{
    public function index(FilterCatalogDesignRequest $request): View
    {
        $validated = $request->validated();
        $query = CatalogDesign::withCount('orders')->with('images')->latest();

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        if (! empty($validated['availability_status'])) {
            $query->where('availability_status', $validated['availability_status']);
        }

        $designs = $query->paginate(15)->withQueryString();

        return view('admin.catalog.index', [
            'designs' => $designs,
            'categories' => config('jewellery.catalog_categories'),
            'availabilityStatuses' => config('jewellery.availability_statuses'),
            'filters' => $request->only(['search', 'category', 'availability_status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.catalog.create', $this->formOptions());
    }

    public function store(StoreCatalogDesignRequest $request): RedirectResponse
    {
        $design = CatalogDesign::create($request->safe()->except('images'));

        $this->storeImages($design, $request->file('images', []));

        return redirect()
            ->route('admin.catalog.index')
            ->with('success', 'Catalog item created successfully.');
    }

    public function edit(CatalogDesign $catalog): View
    {
        $catalog->load('images');

        return view('admin.catalog.edit', array_merge($this->formOptions(), [
            'design' => $catalog,
        ]));
    }

    public function update(UpdateCatalogDesignRequest $request, CatalogDesign $catalog): RedirectResponse
    {
        $catalog->update($request->safe()->except('images'));

        if ($request->hasFile('images')) {
            $this->storeImages($catalog, $request->file('images'));
        }

        return redirect()
            ->route('admin.catalog.edit', $catalog)
            ->with('success', 'Catalog item updated successfully.');
    }

    public function destroy(CatalogDesign $catalog): RedirectResponse
    {
        if ($catalog->orders()->exists()) {
            return back()->with('error', 'Cannot delete an item with existing orders. Mark it as out of stock instead.');
        }

        $this->deleteAllImages($catalog);
        $catalog->delete();

        return redirect()
            ->route('admin.catalog.index')
            ->with('success', 'Catalog item deleted.');
    }

    public function destroyImage(CatalogDesign $catalog, CatalogImage $image): RedirectResponse
    {
        abort_unless($image->catalog_design_id === $catalog->id, 404);

        if ($catalog->images()->count() <= 1) {
            return back()->with('error', 'Cannot delete the only image. Upload a replacement first.');
        }

        Storage::disk('public')->delete($image->image_path);

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $catalog->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    public function setPrimaryImage(CatalogDesign $catalog, CatalogImage $image): RedirectResponse
    {
        abort_unless($image->catalog_design_id === $catalog->id, 404);

        $catalog->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }

    private function formOptions(): array
    {
        return [
            'categories' => config('jewellery.catalog_categories'),
            'goldQualities' => config('jewellery.catalog_gold_qualities'),
            'availabilityStatuses' => config('jewellery.availability_statuses'),
        ];
    }

    private function storeImages(CatalogDesign $design, array $files): void
    {
        $sortOrder = (int) $design->images()->max('sort_order') + 1;
        $hasPrimary = $design->images()->where('is_primary', true)->exists();

        foreach ($files as $file) {
            $path = $file->store('catalog-designs', 'public');

            $design->images()->create([
                'image_path' => $path,
                'sort_order' => $sortOrder++,
                'is_primary' => ! $hasPrimary,
            ]);

            $hasPrimary = true;
        }
    }

    private function deleteAllImages(CatalogDesign $design): void
    {
        foreach ($design->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $design->images()->delete();
    }
}

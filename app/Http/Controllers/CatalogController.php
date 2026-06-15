<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogIndexRequest;
use App\Models\CatalogDesign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(CatalogIndexRequest $request): View
    {
        $validated = $request->validated();
        $query = CatalogDesign::available()->with('images')->orderBy('category')->orderBy('name');

        if (! empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $designs = $query->paginate(12)->withQueryString();

        $allCategories = config('jewellery.catalog_categories');
        $activeCategoryKeys = CatalogDesign::available()->pluck('category')->unique()->sort()->values();
        $categories = collect($allCategories)->only($activeCategoryKeys)->all();

        return view('catalog.index', [
            'designs' => $designs,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function show(CatalogDesign $catalog): View
    {
        abort_unless($catalog->isAvailable(), 404);

        $catalog->load('images');

        return view('catalog.show', ['design' => $catalog]);
    }

    public function purchase(CatalogDesign $catalog): RedirectResponse
    {
        abort_unless($catalog->isAvailable(), 404);

        $orderUrl = route('orders.create', ['catalog' => $catalog->id]);

        if (Auth::user()?->isCustomer()) {
            return redirect($orderUrl);
        }

        session()->put('url.intended', $orderUrl);

        return redirect()
            ->route('register')
            ->with('status', 'Create your account to order '.$catalog->name.'.');
    }

    public function purchaseLogin(CatalogDesign $catalog): RedirectResponse
    {
        abort_unless($catalog->isAvailable(), 404);

        return redirect()->guest(route('orders.create', ['catalog' => $catalog->id]));
    }
}

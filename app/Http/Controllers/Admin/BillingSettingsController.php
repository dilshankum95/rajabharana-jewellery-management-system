<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBillingSettingsRequest;
use App\Models\BillingSetting;
use App\Models\CategoryDiscount;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingSettingsController extends Controller
{
    public function edit(): View
    {
        $billingSetting = BillingSetting::current();
        $billingSetting->load('updatedBy');

        $categoryDiscounts = CategoryDiscount::query()
            ->orderBy('category_code')
            ->get()
            ->keyBy('category_code');

        $categories = config('jewellery.catalog_categories');

        return view('admin.billing.settings', compact('billingSetting', 'categoryDiscounts', 'categories'));
    }

    public function update(UpdateBillingSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        BillingSetting::updateTaxRate(
            (float) $validated['tax_rate_percent'],
            $request->user()->id
        );

        foreach ($validated['category_discounts'] as $code => $percent) {
            CategoryDiscount::query()->updateOrCreate(
                ['category_code' => $code],
                [
                    'discount_percent' => (float) $percent,
                    'is_active' => true,
                    'updated_by' => $request->user()->id,
                ]
            );
        }

        return redirect()
            ->route('admin.billing.settings')
            ->with('success', 'Billing settings updated successfully.');
    }
}

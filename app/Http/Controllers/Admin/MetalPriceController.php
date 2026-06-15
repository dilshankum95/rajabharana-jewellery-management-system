<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMetalPriceRequest;
use App\Models\MetalPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetalPriceController extends Controller
{
    public function edit(): View
    {
        $metalPrice = MetalPrice::current();
        $metalPrice?->load('updatedBy');

        return view('admin.metal-prices.edit', [
            'metalPrice' => $metalPrice,
        ]);
    }

    public function update(UpdateMetalPriceRequest $request): RedirectResponse
    {
        MetalPrice::upsertCurrent(
            (float) $request->validated('gold_price_per_gram'),
            (float) $request->validated('silver_price_per_gram'),
            $request->user()->id,
        );

        return redirect()
            ->route('admin.metal-prices.edit')
            ->with('success', 'Today\'s gold and silver gram prices updated successfully.');
    }
}

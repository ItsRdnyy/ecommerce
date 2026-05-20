<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\PreorderManager;

class PreorderController extends Controller
{
    public function create(Product $product)
    {
        if (!$product->is_preorder) {
            return redirect()->route('products')
                ->with('error', 'Preorder is not available for this product.');
        }

        return view('preorder.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:full,deposit',
        ]);

        if (!$product->is_preorder) {
            return back()->with('error', 'Preorder not available.');
        }

        $quantity = (int) $request->quantity;
        $queue = PreorderManager::queue($product, auth()->user(), $quantity);

        return redirect()->route('preorder.show', $queue)
            ->with('success', 'Preorder placed successfully. Your queue position is #' . PreorderManager::getQueuePosition($queue));
    }

    public function show(\App\Models\PreorderQueue $preorder)
    {
        if ($preorder->user_id !== auth()->id()) {
            abort(403);
        }

        $position = \App\Services\PreorderManager::getQueuePosition($preorder);

        return view('preorder.show', compact('preorder', 'position'));
    }

    public function cancel(\App\Models\PreorderQueue $preorder)
    {
        if ($preorder->user_id !== auth()->id()) {
            abort(403);
        }

        PreorderManager::cancel($preorder);

        return back()->with('success', 'Preorder cancelled.');
    }
}

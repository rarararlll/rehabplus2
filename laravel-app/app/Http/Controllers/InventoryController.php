<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::orderBy('item')->get();

        return view('admin.inventory', [
            'totalStock' => $items->sum('stock'),
            'lowStock' => $items->where('status', 'Low')->count() + $items->where('status', 'Critical')->count(),
            'restockNeeded' => $items->where('stock', '<=', fn ($query) => $query->from('inventory_items')->selectRaw('reorder'))->count(),
            'items' => $items,
        ]);
    }

    public function create(): View
    {
        return view('admin.inventory', [
            'totalStock' => InventoryItem::sum('stock'),
            'lowStock' => 0,
            'restockNeeded' => 0,
            'items' => InventoryItem::orderBy('item')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'reorder' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:Healthy,Low,Critical'],
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully.');
    }

    public function edit(InventoryItem $inventory): View
    {
        return view('admin.inventory', [
            'totalStock' => InventoryItem::sum('stock'),
            'lowStock' => 0,
            'restockNeeded' => 0,
            'items' => InventoryItem::orderBy('item')->get(),
            'inventoryItem' => $inventory,
        ]);
    }

    public function update(Request $request, InventoryItem $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'item' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'reorder' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:Healthy,Low,Critical'],
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory): RedirectResponse
    {
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
    }
}

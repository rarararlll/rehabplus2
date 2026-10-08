@extends('layouts.admin')

@section('title', 'Inventory and Supplies')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="page-title mb-1">Inventory and Supplies</p>
            <p class="page-subtitle mb-0">Track equipment, supplies, stock levels, and restock needs.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header"><h5 class="mb-0">Add inventory item</h5></div>
        <div class="card-body">
            <form method="post" action="{{ route('inventory.store') }}">@csrf
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label small fw-semibold">Item name</label><input type="text" name="item" class="form-control" placeholder="Resistance band" required></div>
                    <div class="col-md-3"><label class="form-label small fw-semibold">Category</label><input type="text" name="category" class="form-control" placeholder="Equipment" required></div>
                    <div class="col-md-2"><label class="form-label small fw-semibold">Stock</label><input type="number" name="stock" class="form-control" placeholder="20" required></div>
                    <div class="col-md-2"><label class="form-label small fw-semibold">Reorder point</label><input type="number" name="reorder" class="form-control" placeholder="10"></div>
                    <div class="col-md-2"><label class="form-label small fw-semibold">Status</label><select name="status" class="form-select"><option value="Healthy">Healthy</option><option value="Low">Low</option><option value="Critical">Critical</option></select></div>
                    <div class="col-12 text-end"><button type="submit" class="btn btn-primary">Add item</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @php($stats = [
            ['label' => 'Total stock', 'value' => $totalStock, 'icon' => 'bi-box', 'tone' => 'text-primary'],
            ['label' => 'Low stock', 'value' => $lowStock.' items', 'icon' => 'bi-exclamation-triangle', 'tone' => 'text-warning'],
            ['label' => 'Restock needed', 'value' => $restockNeeded.' orders', 'icon' => 'bi-arrow-repeat', 'tone' => 'text-info'],
        ])
        @foreach ($stats as $stat)
            <div class="col-md-6 col-xl-4">
                <div class="card stat-card h-100 border-0"><div class="d-flex justify-content-between align-items-center"><div><div class="stat-label">{{ $stat['label'] }}</div><h4 class="stat-value mb-0">{{ $stat['value'] }}</h4></div><i class="bi {{ $stat['icon'] }} stat-icon {{ $stat['tone'] }}"></i></div></div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header"><h5 class="mb-0">Current inventory</h5></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead class="table-light"><tr><th>Item</th><th>Category</th><th>Stock</th><th>Reorder point</th><th>Status</th></tr></thead><tbody>@forelse ($items as $item)<tr><td>{{ $item['item'] }}</td><td>{{ $item['category'] }}</td><td>{{ $item['stock'] ?? 0 }}</td><td>{{ $item['reorder'] ?? 0 }}</td><td>@switch($item['status'] ?? '') @case('Low')<span class="badge bg-warning-subtle text-warning">Low</span>@break @case('Critical')<span class="badge bg-danger-subtle text-danger">Critical</span>@break @default<span class="badge bg-success-subtle text-success">Healthy</span>@endswitch</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No inventory items available.</td></tr>@endforelse</tbody></table></div></div>
    </div>
@endsection

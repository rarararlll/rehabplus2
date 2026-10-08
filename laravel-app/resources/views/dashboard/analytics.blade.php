@extends('layouts.admin')

@section('title', 'RehabPlus Analytics')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><p class="page-title mb-1">Analytics</p><p class="page-subtitle mb-0">Clinical performance trends and recovery indicators.</p></div>
    </div>

    <div class="row g-4 mb-4">
        @php($stats = [
            ['label' => 'Total Patients', 'value' => $totalPatients, 'icon' => 'bi-people-fill', 'tone' => 'text-primary'],
            ['label' => 'Avg Compliance', 'value' => $avgCompliance.'%', 'icon' => 'bi-graph-up-arrow', 'tone' => 'text-success'],
            ['label' => 'Avg Pain', 'value' => $avgPain, 'icon' => 'bi-activity', 'tone' => 'text-warning'],
            ['label' => 'Recovery Data', 'value' => $hasRecoveryData ? 'Live' : 'No data', 'icon' => 'bi-heart-pulse', 'tone' => 'text-info'],
        ])
        @foreach($stats as $stat)
            <div class="col-md-6 col-xl-3"><div class="card stat-card h-100 border-0"><div class="d-flex justify-content-between align-items-center"><div><div class="stat-label">{{ $stat['label'] }}</div><h3 class="stat-value mb-0">{{ $stat['value'] }}</h3></div><i class="bi {{ $stat['icon'] }} stat-icon {{ $stat['tone'] }}"></i></div></div></div>
        @endforeach
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header"><h3 class="mb-0">Recovery Trends</h3></div>
        <div class="card-body p-4">
            @if($hasRecoveryData)
                <p class="fw-bold mb-3">Patient Recovery Index</p>
                <div class="d-flex align-items-end gap-3" style="height:180px;"><div class="d-flex flex-grow-1 justify-content-between align-items-end gap-2" style="height:150px;border-bottom:2px solid var(--border);">@foreach($recoveryValues as $value)<div class="d-flex flex-column align-items-center gap-2 flex-fill"><div style="width:100%;max-width:40px;height:{{ max(20, $value * 1.5) }}px;background:linear-gradient(180deg,#14b8a6,#0f766e);border-radius:6px 6px 0 0;display:block;"></div><small class="text-muted">{{ number_format($value, 0) }}</small></div>@endforeach</div></div>
            @else
                <p class="mb-0">No patient recovery data is available yet.</p>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header"><h3 class="mb-0">Condition Summary</h3></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Condition</th><th>Patients</th></tr></thead><tbody>@foreach($conditionCounts as $condition => $count)<tr><td>{{ $condition }}</td><td>{{ $count }}</td></tr>@endforeach</tbody></table></div></div>
    </div>
@endsection

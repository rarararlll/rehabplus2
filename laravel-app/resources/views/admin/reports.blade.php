@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <p class="page-title mb-1">Reports &amp; Analytics</p>
            <p class="page-subtitle mb-0">Clinic performance overview and patient activity summary.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/reports/export?type=patient-summary') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> Export CSV
            </a>
            <a href="{{ url('/reports/export?type=patient-summary') }}" class="btn btn-primary" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @php($metrics = [
            ['label' => 'Total Registered Patients', 'value' => $totalRegisteredPatients, 'icon' => 'bi-people-fill', 'tone' => 'text-primary'],
            ['label' => 'Active Patients', 'value' => $activePatients, 'icon' => 'bi-person-check-fill', 'tone' => 'text-success'],
            ['label' => 'New Patients This Month', 'value' => $newPatientsThisMonth, 'icon' => 'bi-person-plus-fill', 'tone' => 'text-info'],
            ['label' => 'Total Appointments', 'value' => $totalAppointments, 'icon' => 'bi-calendar-event', 'tone' => 'text-warning'],
            ['label' => 'Completed Appointments', 'value' => $completedAppointments, 'icon' => 'bi-check-circle-fill', 'tone' => 'text-success'],
            ['label' => 'Pending Appointments', 'value' => $pendingAppointments, 'icon' => 'bi-hourglass-split', 'tone' => 'text-secondary'],
            ['label' => 'Cancelled Appointments', 'value' => $cancelledAppointments, 'icon' => 'bi-x-circle-fill', 'tone' => 'text-danger'],
            ['label' => 'Total Revenue', 'value' => $totalRevenue, 'icon' => 'bi-cash-stack', 'tone' => 'text-primary'],
            ['label' => 'Outstanding Payments', 'value' => $outstandingPayments, 'icon' => 'bi-wallet2', 'tone' => 'text-warning'],
            ['label' => 'Treatment Completion Rate', 'value' => $treatmentCompletionRate, 'icon' => 'bi-bar-chart-line-fill', 'tone' => 'text-success'],
        ])

        @foreach ($metrics as $metric)
            <div class="col-md-6 col-xl-4">
                <div class="card stat-card h-100 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ $metric['label'] }}</div>
                            <h3 class="stat-value mb-0">{{ $metric['value'] }}</h3>
                        </div>
                        <i class="bi {{ $metric['icon'] }} stat-icon {{ $metric['tone'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

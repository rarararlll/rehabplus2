@extends('layouts.admin')

@section('title', 'User Accounts')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <p class="page-title mb-1">Roles &amp; Patient Accounts</p>
            <p class="page-subtitle mb-0">Manage clinic staff and rehabilitation patient portal accounts</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-2"></i>Add Staff Account
        </a>
    </div>

    <div class="row g-4 mb-4">
        @php($summary = [
            ['label' => 'Staff Accounts', 'value' => collect($users)->filter(fn($user) => $user->role !== 'patient')->count(), 'icon' => 'bi-person-badge-fill', 'tone' => 'text-success', 'background' => '#dcfce7'],
            ['label' => 'Patient Accounts', 'value' => collect($users)->filter(fn($user) => $user->role === 'patient')->count(), 'icon' => 'bi-people-fill', 'tone' => 'text-primary', 'background' => '#dbeafe'],
            ['label' => 'Active Accounts', 'value' => collect($users)->filter(fn($user) => (int) $user->is_active === 1)->count(), 'icon' => 'bi-check-circle-fill', 'tone' => 'text-info', 'background' => '#dbeafe'],
        ])
        @foreach ($summary as $item)
            <div class="col-md-4">
                <div class="card stat-card h-100 border-0"><div class="d-flex justify-content-between align-items-center"><div><div class="stat-label">{{ $item['label'] }}</div><h1 class="stat-value mb-0">{{ $item['value'] }}</h1></div><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:70px;height:70px;background:{{ $item['background'] }};"><i class="bi {{ $item['icon'] }} {{ $item['tone'] }}" style="font-size:32px;"></i></div></div></div>
            </div>
        @endforeach
    </div>

    <div id="staff-roles" class="card border-0 rounded-5 shadow-sm overflow-hidden mb-4">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0" style="font-size:24px;">Staff Roles</h2>
            <div class="input-group" style="max-width:320px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-4"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 rounded-end-4 py-2" placeholder="Search staff...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background:#f8fafc;"><tr><th class="px-4 py-3 text-muted">ACCOUNT</th><th class="py-3 text-muted">EMAIL</th><th class="py-3 text-muted">ROLE</th><th class="py-3 text-muted text-end pe-5">ACTIONS</th></tr></thead>
                <tbody>@forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3"><div class="d-flex align-items-center gap-3"><div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" style="width:55px;height:55px;font-size:20px;background:linear-gradient(135deg,#14b8a6,#06b6d4);">{{ strtoupper(substr($user->name, 0, 1)) }}</div><div><div class="fw-bold" style="font-size:18px;">{{ $user->name }}</div><small class="text-muted">@switch($user->role) @case('superadmin') System Administrator @break @case('manager') Clinic Manager @break @case('staff') Rehab Staff Personnel @break @case('therapist') Physical Therapist @break @default {{ ucfirst($user->role) }} @endswitch</small></div></div></td>
                        <td style="font-size:16px;">{{ $user->email }}</td>
                        <td>@php($badge = $user->role === 'superadmin' ? 'bg-danger' : ($user->role === 'manager' ? 'bg-warning text-dark' : 'bg-info'))<span class="badge {{ $badge }} rounded-pill px-3 py-2" style="font-size:14px;">{{ ucfirst($user->role) }}</span></td>
                        <td class="text-end pe-5"><a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary rounded-pill px-3 py-2">View</a><a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-primary rounded-pill px-3 py-2">Edit</a><form method="post" action="{{ route('users.delete', $user->id) }}" class="d-inline" onsubmit="return confirm('Delete this staff account?');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2">Delete</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No staff accounts yet.</td></tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

    <div id="patient-accounts" class="card border-0 rounded-5 shadow-sm overflow-hidden">
        <div class="p-4 border-bottom">
            <h2 class="fw-bold mb-2" style="font-size:24px;">Patient Portal Accounts</h2>
            <p class="text-muted mb-0">Give rehabilitation patients access to RehabPlus monitoring tools</p>
        </div>

        <div class="p-4 border-bottom">
            <form action="{{ route('users.createPatient') }}" method="post">
                @csrf
                <input type="hidden" name="role" value="patient">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Patient Full Name</label>
                        <input type="text" name="name" class="form-control rounded-4 py-2" placeholder="Enter patient name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-4 py-2" placeholder="patient@email.com" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Temporary Password</label>
                        <input type="password" name="password" class="form-control rounded-4 py-2" placeholder="Create password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success rounded-4 px-4 py-2 mt-4 fw-semibold">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Create Patient Account
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background:#f8fafc;"><tr><th class="px-4 py-3 text-muted">PATIENT</th><th class="py-3 text-muted">EMAIL</th><th class="py-3 text-muted">ACCOUNT TYPE</th><th class="py-3 text-muted text-end pe-5">ACTIONS</th></tr></thead>
                <tbody>@forelse ($users->where('role', 'patient') as $patient)
                    <tr>
                        <td class="px-4 py-3"><div class="d-flex align-items-center gap-3"><div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" style="width:55px;height:55px;font-size:20px;background:#22c55e;">{{ strtoupper(substr($patient->name, 0, 1)) }}</div><div><div class="fw-bold" style="font-size:18px;">{{ $patient->name }}</div><small class="text-muted">Rehabilitation Patient</small></div></div></td>
                        <td style="font-size:16px;">{{ $patient->email }}</td>
                        <td><span class="badge bg-success rounded-pill px-3 py-2" style="font-size:14px;">Patient</span></td>
                        <td class="text-end pe-5"><a href="{{ route('users.show', $patient->id) }}" class="btn btn-outline-success rounded-pill px-3 py-2">View</a><form method="post" action="{{ route('users.delete', $patient->id) }}" class="d-inline" onsubmit="return confirm('Delete this patient account?');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2">Delete</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No patient portal accounts yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

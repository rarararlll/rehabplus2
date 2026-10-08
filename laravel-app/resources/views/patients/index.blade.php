@extends('layouts.admin')

@section('title', 'Patients')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="page-title mb-1">Patients</p>
            <p class="page-subtitle mb-0">Manage and track all registered patients</p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add Patient
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('patients.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="patient-search" class="form-label small fw-semibold">Search patients</label>
                    <input id="patient-search" type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Name or condition">
                </div>
                <div class="col-md-4">
                    <label for="condition-filter" class="form-label small fw-semibold">Condition</label>
                    <select id="condition-filter" name="condition" class="form-select">
                        <option value="">All conditions</option>
                        @foreach($conditions as $condition)
                            <option value="{{ $condition }}" {{ ($selectedCondition ?? '') === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Patient</th><th>Condition</th><th>Added</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($data as $patient)
                            <tr>
                                <td><div class="d-flex align-items-center gap-3"><span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:38px;height:38px;font-size:.8rem;background:#0e9aaa;flex-shrink:0;">{{ strtoupper(substr($patient->name, 0, 1)) }}</span><a href="{{ route('patients.show', $patient->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $patient->name }}</a></div></td>
                                <td class="text-muted small">{{ $patient->condition }}</td>
                                <td class="text-muted small">{{ $patient->created_at ? $patient->created_at->format('M j, Y g:i A') : '—' }}</td>
                                <td class="text-end"><a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-outline-secondary me-1">View</a><a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a><form method="post" action="{{ route('patients.delete', $patient->id) }}" class="d-inline" onsubmit="return confirm('Delete this patient and all their records?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-5"><i class="bi bi-people fs-3 d-block mb-2 opacity-50"></i>No patients found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

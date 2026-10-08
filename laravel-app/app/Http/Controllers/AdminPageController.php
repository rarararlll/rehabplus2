<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function reports(): View
    {
        return view('admin.reports', [
            'totalRegisteredPatients' => 0,
            'activePatients' => 0,
            'newPatientsThisMonth' => 0,
            'totalAppointments' => 0,
            'completedAppointments' => 0,
            'pendingAppointments' => 0,
            'cancelledAppointments' => 0,
            'totalRevenue' => '₱0.00',
            'outstandingPayments' => '₱0.00',
            'treatmentCompletionRate' => '0%',
        ]);
    }

    public function billing(): View
    {
        return view('admin.billing', [
            'sessionFees' => '₱0.00',
            'paymentStatus' => '0',
            'outstanding' => '₱0.00',
            'paymentHistory' => 0,
            'payments' => [],
        ]);
    }

    public function inventory(): View
    {
        $items = \App\Models\InventoryItem::orderBy('item')->get();

        return view('admin.inventory', [
            'totalStock' => $items->sum('stock'),
            'lowStock' => $items->whereIn('status', ['Low', 'Critical'])->count(),
            'restockNeeded' => $items->filter(fn ($item) => $item->stock <= $item->reorder)->count(),
            'items' => $items,
        ]);
    }

    public function schedule(): View
    {
        $staffDirectory = [
            ['name' => 'Dr. Sarah Mitchell', 'role' => 'Physical Therapist'],
            ['name' => 'Michael Chen', 'role' => 'Occupational Therapist'],
            ['name' => 'Alicia Gomez', 'role' => 'Speech Therapist'],
        ];

        $weekDays = [
            ['label' => 'Monday', 'short' => 'Mon'],
            ['label' => 'Tuesday', 'short' => 'Tue'],
            ['label' => 'Wednesday', 'short' => 'Wed'],
            ['label' => 'Thursday', 'short' => 'Thu'],
            ['label' => 'Friday', 'short' => 'Fri'],
            ['label' => 'Saturday', 'short' => 'Sat'],
            ['label' => 'Sunday', 'short' => 'Sun'],
        ];

        return view('admin.schedule', [
            'weekOffset' => 0,
            'staffDirectory' => $staffDirectory,
            'weekDays' => $weekDays,
            'staffAvailability' => array_fill_keys(array_column($staffDirectory, 'name'), 'Unavailable'),
            'weeklySchedule' => [],
            'todayStaffList' => [],
            'staffCount' => 0,
            'todayShifts' => 0,
            'activeRooms' => 0,
        ]);
    }

    public function notes(): View
    {
        return view('admin.notes', [
            'recentCount' => 0,
            'todayNotes' => 0,
            'notes' => [],
        ]);
    }

    public function assessments(): View
    {
        return view('admin.assessments', [
            'averageScore' => '0%',
            'goalTracking' => '0%',
            'activePatients' => 0,
            'assessments' => [],
        ]);
    }

    public function users(): View
    {
        return view('admin.users', [
            'users' => \App\Models\User::all(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PaymentRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = PaymentRecord::orderByDesc('date')->get();

        return view('admin.billing', [
            'sessionFees' => '₱'.number_format($payments->sum('fee'), 2),
            'paymentStatus' => $payments->where('status', 'Paid')->count(),
            'outstanding' => '₱'.number_format($payments->whereIn('status', ['Pending', 'Partial'])->sum('fee'), 2),
            'paymentHistory' => $payments->count(),
            'payments' => $payments,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient' => ['required', 'string', 'max:150'],
            'session' => ['required', 'string', 'max:150'],
            'fee' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:Paid,Pending,Partial'],
            'date' => ['required', 'date'],
        ]);

        PaymentRecord::create($validated);

        return redirect()->route('billing')->with('success', 'Payment record added successfully.');
    }
}

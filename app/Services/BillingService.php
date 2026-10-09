<?php

namespace App\Services;

use CodeIgniter\Session\SessionInterface;

/**
 * BillingService
 *
 * Owns the session-backed list of billing/payment records that the
 * `DashboardController::billing` and `saveBilling` handlers used to manage
 * inline.
 *
 * Records are stored as plain associative arrays with *numeric* `fee` values.
 * Currency formatting is applied at display time only, so the raw data always
 * reflects what the user actually entered. This keeps `summary()` arithmetic
 * exact and avoids double-rounding.
 *
 * The service accepts a `SessionInterface` in its constructor so it can be
 * unit-tested with the in-memory `ArrayHandler` without booting HTTP.
 */
class BillingService
{
    /** Session key under which the payment records are stored. */
    private const SESSION_KEY = 'billing_records';

    /** Default currency symbol used by `formatPeso`. */
    private const CURRENCY_SYMBOL = '₱';

    public function __construct(private ?SessionInterface $session = null)
    {
        $this->session ??= service('session');
    }

    /**
     * Return every stored payment record, or an empty list when none exist.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $records = $this->session->get(self::SESSION_KEY);

        return is_array($records) ? $records : [];
    }

    /**
     * Validate and persist a new payment record.
     *
     * @param array<string, mixed> $input Raw POST data.
     *
     * @return array<string, string>|null Validation errors keyed by field, or
     *                                     null when the record was saved.
     */
    public function add(array $input): ?array
    {
        $patient = trim((string) ($input['patient'] ?? ''));
        $session = trim((string) ($input['session'] ?? ''));
        $fee     = trim((string) ($input['fee'] ?? ''));
        $status  = trim((string) ($input['status'] ?? ''));
        $date    = trim((string) ($input['date'] ?? ''));

        $errors = $this->validate($patient, $session, $fee, $date);

        if ($errors !== []) {
            log_message('debug', 'BillingService::add rejected input', $errors);

            return $errors;
        }

        $records              = $this->all();
        $records[]           = [
            'patient' => $patient,
            'session' => $session,
            'fee'     => $this->normalizeFee($fee),
            'status'  => $status !== '' ? $status : 'Pending',
            'date'    => $date,
        ];

        $this->session->set(self::SESSION_KEY, $records);
        log_message('info', 'BillingService::add stored payment for ' . $patient, ['total' => count($records)]);

        return null;
    }

    /**
     * Compute the summary values used by the billing view.
     *
     * @return array{totalFees: float, paidCount: int, paymentCount: int,
     *               paymentStatus: string, outstanding: float}
     */
    public function summary(): array
    {
        $records = $this->all();

        $totalFees    = 0.0;
        $paidCount    = 0;
        $paymentCount = count($records);

        foreach ($records as $record) {
            $totalFees += $this->normalizeFee($record['fee'] ?? 0);

            if (strtoupper((string) ($record['status'] ?? '')) === 'PAID') {
                $paidCount++;
            }
        }

        $paymentStatus = $paymentCount > 0
            ? round(($paidCount / $paymentCount) * 100) . '% collected'
            : '0% collected';

        return [
            'totalFees'      => $totalFees,
            'paidCount'      => $paidCount,
            'paymentCount'   => $paymentCount,
            'paymentStatus'  => $paymentStatus,
            'outstanding'    => max(0.0, $totalFees * 0.35),
        ];
    }

    /**
     * Validate the required billing fields.
     *
     * @return array<string, string>
     */
    private function validate(string $patient, string $session, string $fee, string $date): array
    {
        $errors = [];

        if ($patient === '') {
            $errors['patient'] = 'Patient name is required.';
        }

        if ($session === '') {
            $errors['session'] = 'Session description is required.';
        }

        if ($fee === '' || ! is_numeric($fee)) {
            $errors['fee'] = 'A valid fee amount is required.';
        }

        if ($date === '') {
            $errors['date'] = 'A date is required.';
        }

        return $errors;
    }

    /**
     * Convert any user-supplied fee value into a clean float.
     *
     * Accepts plain numbers ("1250"), currency-formatted strings ("₱1,250.00")
     * and arbitrary junk, which is reduced to 0.0. This is intentionally
     * tolerant: the controller stores whatever the user typed, and we only
     * ever assert the numeric result.
     */
    public function normalizeFee($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return (float) ($clean !== '' ? $clean : 0);
    }

    /**
     * Format a numeric amount as a peso string for display.
     */
    public function formatPeso(float $amount): string
    {
        return self::CURRENCY_SYMBOL . ' ' . number_format($amount, 2, '.', ',');
    }
}
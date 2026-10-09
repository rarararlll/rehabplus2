<?php

namespace App\Services;

use App\Models\UserModel;
use CodeIgniter\Session\SessionInterface;

/**
 * AssessmentsService
 *
 * Owns the session-backed list of patient assessments that the
 * `DashboardController::assessments` and `saveAssessments` handlers used to
 * manage inline.
 *
 * The `averageScore` and `goalTracking` values are computed from the stored
 * records rather than hard-coded, so the dashboard reflects real data.
 */
class AssessmentsService
{
    /** Session key under which the assessments are stored. */
    private const SESSION_KEY = 'patient_assessments';

    public function __construct(private ?SessionInterface $session = null)
    {
        $this->session ??= service('session');
    }

    /**
     * Return every stored assessment, or an empty list when none exist.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $assessments = $this->session->get(self::SESSION_KEY);

        return is_array($assessments) ? $assessments : [];
    }

    /**
     * Validate and persist a new assessment.
     *
     * @param array<string, mixed> $input Raw POST data.
     *
     * @return array<string, string>|null Validation errors keyed by field, or
     *                                     null when the assessment was saved.
     */
    public function add(array $input): ?array
    {
        $patient    = trim((string) ($input['patient'] ?? ''));
        $therapist  = trim((string) ($input['therapist'] ?? ''));
        $type       = trim((string) ($input['type'] ?? ''));
        $score      = trim((string) ($input['score'] ?? ''));
        $goal       = trim((string) ($input['goal'] ?? ''));
        $date       = trim((string) ($input['date'] ?? ''));

        $errors = $this->validate($patient, $therapist, $type, $score, $goal, $date);

        if ($errors !== []) {
            log_message('debug', 'AssessmentsService::add rejected input', $errors);

            return $errors;
        }

        $assessments    = $this->all();
        $assessments[] = [
            'patient'   => $patient,
            'therapist' => $therapist,
            'type'      => $type,
            'score'     => $score,
            'goal'      => $goal,
            'date'      => $date,
        ];

        $this->session->set(self::SESSION_KEY, $assessments);
        log_message('info', 'AssessmentsService::add stored assessment for ' . $patient, ['total' => count($assessments)]);

        return null;
    }

    /**
     * Compute the summary values used by the assessments view.
     *
     * @return array{assessments: list<array<string, mixed>>, averageScore: string,
     *               goalTracking: int, activePatients: int}
     */
    public function summary(): array
    {
        $assessments = $this->all();

        $totalScore = 0.0;
        $parsed     = 0;

        foreach ($assessments as $record) {
            $score = $this->normalizeScore($record['score'] ?? '');

            if ($score !== null) {
                $totalScore += $score;
                $parsed++;
            }
        }

        $averageScore = $parsed > 0 ? round($totalScore / $parsed, 1) . '%' : '0%';

        return [
            'assessments'  => $assessments,
            'averageScore' => $averageScore,
            'goalTracking' => count($assessments),
            'activePatients' => count($assessments),
        ];
    }

    /**
     * Validate the required assessment fields.
     *
     * @return array<string, string>
     */
    private function validate(string $patient, string $therapist, string $type, string $score, string $goal, string $date): array
    {
        $errors = [];

        if ($patient === '') {
            $errors['patient'] = 'Patient name is required.';
        }

        if ($therapist === '') {
            $errors['therapist'] = 'Therapist name is required.';
        }

        if ($type === '') {
            $errors['type'] = 'Assessment type is required.';
        }

        if ($score === '' || ! is_numeric($score)) {
            $errors['score'] = 'A valid score is required.';
        }

        if ($goal === '') {
            $errors['goal'] = 'A goal is required.';
        }

        if ($date === '') {
            $errors['date'] = 'A date is required.';
        }

        return $errors;
    }

    /**
     * Parse a user-supplied score into a float, or null when it cannot be
     * interpreted. Accepts plain numbers ("85") and percentages ("85%").
     */
    private function normalizeScore($value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return $clean !== '' ? (float) $clean : null;
    }
}
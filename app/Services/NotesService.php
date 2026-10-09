<?php

namespace App\Services;

use CodeIgniter\Session\SessionInterface;

/**
 * NotesService
 *
 * Owns the session-backed list of therapy notes that the
 * `DashboardController::notes` and `saveNotes` handlers used to manage inline.
 *
 * Each note is a free-form clinical record. Validation is intentionally
 * lightweight (required fields only) because the content is narrative.
 */
class NotesService
{
    /** Session key under which the therapy notes are stored. */
    private const SESSION_KEY = 'therapy_notes';

    public function __construct(private ?SessionInterface $session = null)
    {
        $this->session ??= service('session');
    }

    /**
     * Return every stored therapy note, or an empty list when none exist.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $notes = $this->session->get(self::SESSION_KEY);

        return is_array($notes) ? $notes : [];
    }

    /**
     * Validate and persist a new therapy note.
     *
     * @param array<string, mixed> $input Raw POST data.
     *
     * @return array<string, string>|null Validation errors keyed by field, or
     *                                     null when the note was saved.
     */
    public function add(array $input): ?array
    {
        $patient    = trim((string) ($input['patient'] ?? ''));
        $therapist  = trim((string) ($input['therapist'] ?? ''));
        $topic      = trim((string) ($input['topic'] ?? ''));
        $note       = trim((string) ($input['note'] ?? ''));
        $date       = trim((string) ($input['date'] ?? ''));

        $errors = $this->validate($patient, $therapist, $topic, $note, $date);

        if ($errors !== []) {
            log_message('debug', 'NotesService::add rejected input', $errors);

            return $errors;
        }

        $notes      = $this->all();
        $notes[]   = [
            'patient'   => $patient,
            'therapist' => $therapist,
            'topic'     => $topic,
            'note'      => $note,
            'date'      => $date,
        ];

        $this->session->set(self::SESSION_KEY, $notes);
        log_message('info', 'NotesService::add stored note for ' . $patient, ['total' => count($notes)]);

        return null;
    }

    /**
     * Compute the summary values used by the notes view.
     *
     * @return array{notes: list<array<string, mixed>>, recentCount: int,
     *               todayNotes: int}
     */
    public function summary(): array
    {
        $notes = $this->all();
        $today = date('Y-m-d');

        $todayNotes = 0;
        foreach ($notes as $record) {
            if ((string) ($record['date'] ?? '') === $today) {
                $todayNotes++;
            }
        }

        return [
            'notes'       => $notes,
            'recentCount' => count($notes),
            'todayNotes'  => $todayNotes,
        ];
    }

    /**
     * Validate the required note fields.
     *
     * @return array<string, string>
     */
    private function validate(string $patient, string $therapist, string $topic, string $note, string $date): array
    {
        $errors = [];

        if ($patient === '') {
            $errors['patient'] = 'Patient name is required.';
        }

        if ($therapist === '') {
            $errors['therapist'] = 'Therapist name is required.';
        }

        if ($topic === '') {
            $errors['topic'] = 'Topic is required.';
        }

        if ($note === '') {
            $errors['note'] = 'Note content is required.';
        }

        if ($date === '') {
            $errors['date'] = 'A date is required.';
        }

        return $errors;
    }
}
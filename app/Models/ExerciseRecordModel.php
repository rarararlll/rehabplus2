<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ExerciseRecordModel
 *
 * Stores a patient's daily exercise log: the prescribed vs completed sets,
 * pain level and the recorded timestamp. Also exposes the dashboard
 * analytics queries used by `DashboardController`.
 */
class ExerciseRecordModel extends Model
{
    protected $table      = 'exercise_records';
    protected $primaryKey = 'id';
    protected $allowedFields = ['patient_id', 'exercise_name', 'sets_prescribed', 'sets_completed', 'pain_level', 'notes', 'recorded_at'];
    protected $useTimestamps = true;

    /**
     * Returns per-patient stats: compliance_rate, avg_pain, total_sessions, recovery_score
     *
     * The query uses the configured table prefix so it works against both the
     * production MySQL database and the SQLite in-memory test database.
     */
    public function getPatientStats(): array
    {
        $patients   = $this->db->DBPrefix . 'patients';
        $exercise   = $this->db->DBPrefix . 'exercise_records';

        $sql = "
            SELECT
                p.id,
                p.name,
                p.condition,
                COUNT(er.id)                                                        AS total_sessions,
                COALESCE(ROUND(SUM(er.sets_completed) / NULLIF(SUM(er.sets_prescribed), 0) * 100, 1), 0) AS compliance_rate,
                COALESCE(ROUND(AVG(er.pain_level), 1), 0) AS avg_pain,
                COALESCE(ROUND((SUM(er.sets_completed) / NULLIF(SUM(er.sets_prescribed), 0) * 100)
                    - (AVG(er.pain_level) * 5), 1), 0) AS recovery_score
            FROM {$patients} p
            LEFT JOIN {$exercise} er ON er.patient_id = p.id
            GROUP BY p.id, p.name, p.condition
            ORDER BY p.name
        ";

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * Returns recent exercise records with the patient name.
     *
     * The `select()` / `join()` helpers apply the configured table prefix
     * automatically, so no manual prefix handling is needed here.
     */
    public function getRecentRecords(int $limit = 10): array
    {
        return $this->select('exercise_records.*, patients.name AS patient_name')
                    ->join('patients', 'patients.id = exercise_records.patient_id')
                    ->orderBy('recorded_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RepairPatientProfiles extends Migration
{
    public function up(): void
    {
        $patients = $this->db->DBPrefix . 'patients';
        $users   = $this->db->DBPrefix . 'users';

        // Create a patient profile for every patient-role user that does not
        // already have one. `INSERT INTO ... SELECT ... WHERE NOT EXISTS`
        // works on both MySQL and SQLite, so we avoid driver-specific SQL.
        $this->db->query(
            "INSERT INTO {$patients} (user_id, name, `condition`)
             SELECT u.id, u.name, 'Not specified'
             FROM {$users} AS u
             WHERE u.role = 'patient'
               AND NOT EXISTS (
                   SELECT 1
                   FROM {$patients} AS p
                   WHERE p.user_id = u.id
               )"
        );
    }

    public function down(): void
    {
        $patients = $this->db->DBPrefix . 'patients';
        $users   = $this->db->DBPrefix . 'users';

        if ($this->db->DBDriver === 'SQLite3') {
            // SQLite rejects `DELETE ... FROM ... JOIN`, so use a subquery.
            $this->db->query(
                "DELETE FROM {$patients}
                 WHERE user_id IN (
                     SELECT u.id
                     FROM {$users} AS u
                     WHERE u.role = 'patient'
                 ) AND `condition` = 'Not specified'"
            );
        } else {
            $this->db->query(
                "DELETE p FROM {$patients} AS p
                 INNER JOIN {$users} AS u ON u.id = p.user_id
                 WHERE u.role = 'patient' AND p.`condition` = 'Not specified'"
            );
        }
    }
}
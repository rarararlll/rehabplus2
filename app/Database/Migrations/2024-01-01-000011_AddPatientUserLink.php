<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPatientUserLink extends Migration
{
    public function up(): void
    {
        // SQLite cannot add a UNIQUE column without a default value, so we
        // omit the unique constraint there. The uniqueness is enforced by the
        // application logic (UserController::createPatient) on every driver.
        $definition = [
            'user_id' => [
                'type'       => 'INT',
                'null'       => true,
                'unique'     => $this->db->DBDriver !== 'SQLite3',
                'after'      => 'id',
            ],
        ];

        $this->forge->addColumn('patients', $definition);

        $patients = $this->db->DBPrefix . 'patients';
        $users   = $this->db->DBPrefix . 'users';

        if ($this->db->DBDriver === 'SQLite3') {
            // SQLite rejects `UPDATE ... JOIN` and `UPDATE ... AS alias`, so
            // use a correlated subquery instead.
            $this->db->query(
                "UPDATE {$patients}
                 SET user_id = (
                     SELECT u.id
                     FROM {$users} AS u
                     WHERE u.name = {$patients}.name
                       AND u.role = 'patient'
                 )
                 WHERE user_id IS NULL"
            );
        } else {
            $this->db->query(
                "UPDATE {$patients} AS p
                 JOIN {$users} AS u
                   ON u.name = p.name AND u.role = 'patient'
                 SET p.user_id = u.id
                 WHERE p.user_id IS NULL"
            );
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('patients', 'user_id');
    }
}
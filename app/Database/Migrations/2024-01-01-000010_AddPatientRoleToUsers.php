<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPatientRoleToUsers extends Migration
{
    public function up(): void
    {
        // SQLite has no ALTER TABLE ... MODIFY, so we no-op there. The role
        // column is already a VARCHAR on SQLite, so any value is accepted.
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->db->query(
                "ALTER TABLE {$this->db->DBPrefix}users MODIFY role ENUM('superadmin', 'manager', 'staff', 'therapist', 'patient') NOT NULL DEFAULT 'staff'"
            );
        }

        $this->db->query("UPDATE {$this->db->DBPrefix}users SET role = 'patient' WHERE role = ''");
    }

    public function down(): void
    {
        $this->db->query("DELETE FROM {$this->db->DBPrefix}users WHERE role = 'patient'");

        if ($this->db->DBDriver !== 'SQLite3') {
            $this->db->query(
                "ALTER TABLE {$this->db->DBPrefix}users MODIFY role ENUM('superadmin', 'manager', 'staff', 'therapist') NOT NULL DEFAULT 'staff'"
            );
        }
    }
}
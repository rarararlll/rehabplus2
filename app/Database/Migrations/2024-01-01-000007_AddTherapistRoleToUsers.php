<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTherapistRoleToUsers extends Migration
{
    public function up(): void
    {
        // SQLite has no ALTER TABLE ... MODIFY, so we no-op there. The role
        // column is already a VARCHAR on SQLite, so any value is accepted.
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->db->query(
                "ALTER TABLE users MODIFY role ENUM('superadmin', 'manager', 'staff', 'therapist') NOT NULL DEFAULT 'staff'"
            );
        }
    }

    public function down(): void
    {
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->db->query(
                "ALTER TABLE users MODIFY role ENUM('superadmin', 'manager', 'staff') NOT NULL DEFAULT 'staff'"
            );
        }
    }
}
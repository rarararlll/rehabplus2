<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('migrations', function (Blueprint $table) {
            if (! Schema::hasColumn('migrations', 'migration')) {
                $table->string('migration')->nullable()->after('batch');
            }
        });

        DB::statement('ALTER TABLE migrations MODIFY version VARCHAR(255) NULL');
        DB::statement('ALTER TABLE migrations MODIFY class VARCHAR(255) NULL');
        DB::statement('ALTER TABLE migrations MODIFY `group` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE migrations MODIFY namespace VARCHAR(255) NULL');
        DB::statement('ALTER TABLE migrations MODIFY time INT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE migrations MODIFY version VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE migrations MODIFY class VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE migrations MODIFY `group` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE migrations MODIFY namespace VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE migrations MODIFY time INT NOT NULL');
        DB::statement('ALTER TABLE migrations DROP COLUMN IF EXISTS migration');
    }
};

<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Base test case for the RehabPlus application.
 *
 * - Runs all application migrations (namespace `App`) against the in-memory
 *   SQLite test database defined in `Config\Database::$tests`.
 * - Seeds the test database with `UserSeeder` (staff accounts) and
 *   `ClinicalSeeder` (patients + exercise records) before every test class.
 *
 * Every application test should extend this class instead of using
 * `DatabaseTestTrait` directly so migration/seeding behaviour stays in one
 * place and stays consistent.
 */
abstract class RehabPlusTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /** Run migrations from the `App` namespace (app/Database/Migrations). */
    protected $namespace = 'App';

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    /** Seed users first (staff accounts), then clinical data (patients/exercises). */
    protected $seed = [
        'App\Database\Seeds\UserSeeder',
        'App\Database\Seeds\ClinicalSeeder',
    ];
}
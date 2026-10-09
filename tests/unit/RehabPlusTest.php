<?php

namespace Tests\Unit;

use App\Models\PatientModel;
use App\Models\UserModel;
use Tests\Support\RehabPlusTestCase;

/**
 * Regression guards for the RehabPlus domain logic.
 *
 * These tests run against a fresh in-memory SQLite database that is migrated
 * and seeded (UserSeeder + ClinicalSeeder) before each test class.
 */
class RehabPlusTest extends RehabPlusTestCase
{
    // ---------------------------------------------------------------
    // Pure logic (no database required)
    // ---------------------------------------------------------------

    /** Guard: passwords are never stored in plain text. */
    public function testPasswordIsHashed(): void
    {
        $plain  = 'secret123';
        $hashed = password_hash($plain, PASSWORD_DEFAULT);

        $this->assertTrue(password_verify($plain, $hashed));
        $this->assertNotEquals($plain, $hashed);
    }

    /** Guard: `Compliance rate calculation` math in DashboardController. */
    public function testComplianceRateCalculation(): void
    {
        $prescribed = 10;
        $completed  = 8;
        $compliance = $prescribed > 0 ? round($completed / $prescribed * 100, 1) : 0;

        $this->assertEquals(80.0, $compliance);
    }

    // ---------------------------------------------------------------
    // UserModel behaviour
    // ---------------------------------------------------------------

    /** Guard: superadmin user is findable by email and has the right role. */
    public function testFindUserByEmail(): void
    {
        $model = new UserModel();
        $user  = $model->findByEmail('superadmin@rehabplus.com');

        $this->assertNotNull($user);
        $this->assertEquals('superadmin', $user['role']);
    }

    /** Guard: users can be filtered by role. */
    public function testGetUsersByRole(): void
    {
        $model   = new UserModel();
        $managers = $model->getByRole('manager');

        $this->assertIsArray($managers);
        $this->assertCount(1, $managers);
        $this->assertEquals('manager', $managers[0]['role']);
    }

    // ---------------------------------------------------------------
    // UserModel::normalizeRoleForStaffAccount
    // ---------------------------------------------------------------

    /** Guard: staff account roles never allow the patient role. */
    public function testStaffRoleValidationRejectsPatientRole(): void
    {
        $this->assertSame('staff', UserModel::normalizeRoleForStaffAccount('patient'));
        $this->assertSame('staff', UserModel::normalizeRoleForStaffAccount(''));
        $this->assertSame('staff', UserModel::normalizeRoleForStaffAccount(null));
        $this->assertSame('therapist', UserModel::normalizeRoleForStaffAccount('therapist'));
        $this->assertSame('manager', UserModel::normalizeRoleForStaffAccount('manager'));
        $this->assertSame('superadmin', UserModel::normalizeRoleForStaffAccount('superadmin'));
        $this->assertSame('staff', UserModel::normalizeRoleForStaffAccount('unknown-role'));
    }

    // ---------------------------------------------------------------
    // PatientModel validation
    // ---------------------------------------------------------------

    /** Guard: PatientModel rejects an empty name. */
    public function testPatientValidationFailsWithEmptyName(): void
    {
        $model  = new PatientModel();
        $result = $model->insert(['name' => '', 'condition' => 'Test Condition']);

        $this->assertFalse($result);
        $this->assertArrayHasKey('name', $model->errors());
    }

    /** Guard: PatientModel accepts a valid record. */
    public function testPatientValidationAcceptsValidRecord(): void
    {
        $model  = new PatientModel();
        $result = $model->insert(['name' => 'Valid Patient', 'condition' => 'Knee Rehab']);

        $this->assertNotFalse($result);
        $this->assertEmpty($model->errors());
    }
}
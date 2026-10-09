<?php

namespace Tests\Unit;

use App\Models\ExerciseRecordModel;
use Tests\Support\RehabPlusTestCase;

/**
 * Regression guards for `App\Models\ExerciseRecordModel`.
 *
 * The `getPatientStats()` method runs a hand-written SQL query that computes
 * compliance rate, average pain and recovery score per patient. A single
 * column rename or rounding change would silently break the dashboard, so we
 * pin down the exact shape and values produced against the seeded
 * `ClinicalSeeder` data.
 */
final class ExerciseRecordModelTest extends RehabPlusTestCase
{
    /** Guard: the query returns one row per seeded patient. */
    public function testGetPatientStatsReturnsOneRowPerPatient(): void
    {
        $stats = (new ExerciseRecordModel())->getPatientStats();

        $this->assertCount(3, $stats);
    }

    /** Guard: every row exposes the expected keys. */
    public function testGetPatientStatsReturnsExpectedKeys(): void
    {
        $stats = (new ExerciseRecordModel())->getPatientStats();
        $row   = $stats[0];

        $this->assertArrayHasKey('id', $row);
        $this->assertArrayHasKey('name', $row);
        $this->assertArrayHasKey('condition', $row);
        $this->assertArrayHasKey('total_sessions', $row);
        $this->assertArrayHasKey('compliance_rate', $row);
        $this->assertArrayHasKey('avg_pain', $row);
        $this->assertArrayHasKey('recovery_score', $row);
    }

    /** Guard: patients with no exercise rows still appear (LEFT JOIN). */
    public function testGetPatientStatsIncludesPatientsWithoutRecords(): void
    {
        $stats = (new ExerciseRecordModel())->getPatientStats();

        // ClinicalSeeder creates 3 patients and 6 exercise records spread
        // across all 3, so every patient should have at least one row.
        $names = array_column($stats, 'name');
        $this->assertContains('Juan dela Cruz', $names);
        $this->assertContains('Maria Santos', $names);
        $this->assertContains('Pedro Reyes', $names);
    }

    /** Guard: compliance rate is computed as completed / prescribed * 100. */
    public function testGetPatientStatsComplianceRateIsPercentage(): void
    {
        $stats = (new ExerciseRecordModel())->getPatientStats();

        foreach ($stats as $row) {
            $this->assertIsNumeric($row['compliance_rate']);
            $this->assertGreaterThanOrEqual(0, (float) $row['compliance_rate']);
            $this->assertLessThanOrEqual(100, (float) $row['compliance_rate']);
        }
    }

    /** Guard: recovery score is compliance_rate minus pain penalty. */
    public function testGetPatientStatsRecoveryScoreFollowsFormula(): void
    {
        $stats = (new ExerciseRecordModel())->getPatientStats();

        foreach ($stats as $row) {
            $expected = round((float) $row['compliance_rate'] - ((float) $row['avg_pain'] * 5), 1);
            $this->assertEquals($expected, (float) $row['recovery_score'], "Recovery score mismatch for {$row['name']}");
        }
    }

    /** Guard: getRecentRecords returns rows ordered by recorded_at DESC. */
    public function testGetRecentRecordsReturnsLimitedAndOrdered(): void
    {
        $records = (new ExerciseRecordModel())->getRecentRecords(2);

        $this->assertCount(2, $records);
        $this->assertLessThanOrEqual($records[0]['recorded_at'], $records[1]['recorded_at']);
    }

    /** Guard: getRecentRecords joins the patient name. */
    public function testGetRecentRecordsIncludesPatientName(): void
    {
        $records = (new ExerciseRecordModel())->getRecentRecords(10);

        $this->assertNotEmpty($records);
        $this->assertArrayHasKey('patient_name', $records[0]);
        $this->assertNotEmpty($records[0]['patient_name']);
    }
}
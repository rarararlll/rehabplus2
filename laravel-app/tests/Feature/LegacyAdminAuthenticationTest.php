<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyAdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_super_admin_can_log_in_and_log_out(): void
    {
        DB::table('super_admins')->insert([
            'email' => 'admin@rehabplus.com',
            'password' => password_hash('correct-password', PASSWORD_BCRYPT),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $loginResponse = $this->post('/login', [
            'email' => 'admin@rehabplus.com',
            'password' => 'correct-password',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertSame('superadmin', Auth::user()->role);

        $logoutResponse = $this->post('/logout');

        $logoutResponse->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}

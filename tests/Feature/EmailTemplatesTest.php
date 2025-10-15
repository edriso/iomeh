<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordEmail;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_email_renders_correctly(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $verificationUrl = 'https://iomeh.com/verify-email?token=abc123';
        $mailable = new WelcomeEmail($user, $verificationUrl);
        
        $mailable->assertSeeInHtml('Welcome to IOMEH');
        $mailable->assertSeeInHtml('John Doe');
        $mailable->assertSeeInHtml($verificationUrl);
    }

    public function test_verify_email_renders_correctly(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ]);

        $verificationUrl = 'https://iomeh.com/verify-email?token=abc123';
        $mailable = new VerifyEmail($user, $verificationUrl);
        
        $mailable->assertSeeInHtml('Verify Your Email');
        $mailable->assertSeeInHtml('Jane Smith');
        $mailable->assertSeeInHtml($verificationUrl);
    }

    public function test_reset_password_email_renders_correctly(): void
    {
        $user = User::factory()->create([
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com'
        ]);

        $resetUrl = 'https://iomeh.com/reset-password?token=xyz789';
        $mailable = new ResetPasswordEmail($user, $resetUrl);
        
        $mailable->assertSeeInHtml('Password Reset');
        $mailable->assertSeeInHtml('Bob Wilson');
        $mailable->assertSeeInHtml($resetUrl);
    }
}

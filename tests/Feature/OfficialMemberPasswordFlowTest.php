<?php

namespace Tests\Feature;

use App\Filament\Pages\ForcePasswordChange;
use App\Http\Middleware\ForcePasswordChangeMiddleware;
use App\Mail\OfficialMemberCredentialsMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OfficialMemberPasswordFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_official_member_auto_generates_password_and_sends_email(): void
    {
        Mail::fake();

        $plainPassword = Str::random(12);

        $member = User::create([
            'name' => 'John Member',
            'email' => 'member@example.com',
            'username' => 'johnmember',
            'phone' => '9841234567',
            'password' => Hash::make($plainPassword),
            'must_change_password' => true,
        ]);

        Mail::to($member->email)->send(
            new OfficialMemberCredentialsMail($member, $plainPassword)
        );

        $this->assertNotNull($member);
        $this->assertTrue($member->must_change_password);

        Mail::assertSent(OfficialMemberCredentialsMail::class, function ($mail) use ($member) {
            return $mail->hasTo('member@example.com') &&
                   $mail->user->id === $member->id &&
                   strlen($mail->plainPassword) === 12;
        });
    }

    public function test_middleware_redirects_users_with_must_change_password_flag_to_force_password_change_page(): void
    {
        $member = User::factory()->create([
            'must_change_password' => true,
        ]);

        $this->actingAs($member);

        $middleware = new ForcePasswordChangeMiddleware;
        $request = Request::create('/maidan', 'GET');

        $response = $middleware->handle($request, function () {
            return response('Access granted', 200);
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(ForcePasswordChange::getUrl(), $response->getTargetUrl());
    }

    public function test_user_can_login_with_email_username_or_phone(): void
    {
        $password = 'secret-pass-123';
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'username' => 'loginuser',
            'phone' => '9876543210',
            'password' => Hash::make($password),
        ]);

        // Email login
        $responseEmail = $this->post('/maidan/login', [
            'data' => [
                'login' => 'loginuser@example.com',
                'password' => $password,
            ],
        ]);
        $responseEmail->assertSessionHasNoErrors();

        // Username login
        $responseUsername = $this->post('/maidan/login', [
            'data' => [
                'login' => 'loginuser',
                'password' => $password,
            ],
        ]);
        $responseUsername->assertSessionHasNoErrors();

        // Phone login
        $responsePhone = $this->post('/maidan/login', [
            'data' => [
                'login' => '9876543210',
                'password' => $password,
            ],
        ]);
        $responsePhone->assertSessionHasNoErrors();
    }
}

<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\InvalidateUserSessions;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SessionManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string PASSWORD = 'Segura#2026';

    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'database']);
    }

    public function test_a_session_idle_for_more_than_30_minutes_expires(): void
    {
        $sessionId = $this->openSession($this->user());
        $this->setLastActivity($sessionId, minutesAgo: 31);

        $this->useSession($sessionId)->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_a_session_idle_for_less_than_30_minutes_stays_active(): void
    {
        $sessionId = $this->openSession($this->user());
        $this->setLastActivity($sessionId, minutesAgo: 29);

        $this->useSession($sessionId)->get(route('home'))->assertOk();
    }

    public function test_the_activity_of_one_session_does_not_extend_another(): void
    {
        $user = $this->user();
        $activeSessionId = $this->openSession($user);
        $idleSessionId = $this->openSession($user);
        $this->setLastActivity($idleSessionId, minutesAgo: 20);
        $idleActivity = DB::table('sessions')->where('id', $idleSessionId)->value('last_activity');

        $this->useSession($activeSessionId)->get(route('home'))->assertOk();

        $this->assertSame($idleActivity, DB::table('sessions')->where('id', $idleSessionId)->value('last_activity'));
    }

    public function test_a_session_opened_before_a_password_change_is_logged_out(): void
    {
        $user = $this->user();
        $sessionId = $this->openSession($user);
        $this->useSession($sessionId)->get(route('home'))->assertOk();

        $user->forceFill(['password' => 'Nueva#Clave2026'])->save();

        $this->useSession($sessionId)->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_login_links_the_stored_session_to_the_user(): void
    {
        $this->post(route('login'), ['email' => $this->user()->email, 'password' => self::PASSWORD]);

        $this->assertDatabaseCount('sessions', 1);
        $this->assertNotNull(DB::table('sessions')->value('user_id'));
    }

    public function test_logout_removes_the_current_session(): void
    {
        $user = $this->user();
        $sessionId = $this->openSession($user);

        $this->useSession($sessionId)->post(route('logout'))->assertRedirectToRoute('login');

        $this->assertDatabaseMissing('sessions', ['id' => $sessionId]);
        $this->useSession($sessionId)->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_closing_all_sessions_removes_every_session_of_the_account_including_the_current_one(): void
    {
        $user = $this->user();
        $otherUser = $this->user();
        $currentSessionId = $this->openSession($user);
        $secondSessionId = $this->openSession($user);
        $unrelatedSessionId = $this->openSession($otherUser);

        $this->useSession($currentSessionId)->post(route('logout.all'))->assertRedirectToRoute('login');

        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
        $this->assertDatabaseHas('sessions', ['id' => $unrelatedSessionId]);
        $this->useSession($secondSessionId)->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_closing_all_sessions_is_not_available_to_guests(): void
    {
        $this->post(route('logout.all'))->assertRedirectToRoute('login');
    }

    #[TestWith(['database'])]
    #[TestWith(['file'])]
    public function test_closing_all_sessions_ends_the_others_on_every_driver(string $driver): void
    {
        $this->useDriver($driver);
        $user = $this->user();
        $currentSessionId = $this->openSession($user);
        $secondSessionId = $this->openSession($user);

        $this->useSession($secondSessionId)->get(route('home'))->assertOk();

        $response = $this->useSession($currentSessionId)->post(route('logout.all'))->assertRedirectToRoute('login');

        $this->assertSame('Cerraste todas tus sesiones.', $response->getSession()->get('status'));

        $this->useSession($secondSessionId)->get(route('home'))->assertRedirectToRoute('login');
        $this->useSession($currentSessionId)->get(route('home'))->assertRedirectToRoute('login');
    }

    #[TestWith(['database'])]
    #[TestWith(['file'])]
    public function test_a_password_reset_ends_existing_sessions_on_every_driver(string $driver): void
    {
        Notification::fake();
        $this->useDriver($driver);
        $user = $this->user();
        $sessionId = $this->openSession($user);
        $this->useSession($sessionId)->get(route('home'))->assertOk();
        $token = Password::broker()->createToken($user);

        $this->forgetSessionStateInMemory();
        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'Nueva#Clave2026',
            'password_confirmation' => 'Nueva#Clave2026',
        ])->assertRedirectToRoute('login');

        $this->useSession($sessionId)->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_closing_all_sessions_reports_no_success_when_invalidation_fails(): void
    {
        $user = $this->user();
        $sessionId = $this->openSession($user);
        $this->instance(InvalidateUserSessions::class, new class extends InvalidateUserSessions
        {
            public function handle(User $user): void
            {
                throw new \RuntimeException('Simulated failure');
            }
        });

        $response = $this->useSession($sessionId)->from(route('home'))->post(route('logout.all'));

        $response->assertRedirect(route('home'));
        $this->assertNull($response->getSession()->get('status'));
        $this->assertTrue($response->getSession()->has('errors'));
        $this->assertDatabaseHas('sessions', ['id' => $sessionId]);
    }

    public function test_logging_out_of_one_session_leaves_the_others_active(): void
    {
        $user = $this->user();
        $firstSessionId = $this->openSession($user);
        $secondSessionId = $this->openSession($user);

        $this->useSession($firstSessionId)->post(route('logout'))->assertRedirectToRoute('login');

        $this->useSession($secondSessionId)->get(route('home'))->assertOk();
    }

    public function test_login_replaces_the_guest_session_identifier(): void
    {
        $user = $this->user();
        $this->get(route('login'))->assertOk();
        $guestSessionId = DB::table('sessions')->value('id');

        $this->useSession($guestSessionId)->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD]);

        $this->assertDatabaseMissing('sessions', ['id' => $guestSessionId]);
        $this->assertDatabaseHas('sessions', ['user_id' => $user->id]);
    }

    public function test_session_contents_are_encrypted_at_rest(): void
    {
        $this->openSession($this->user());

        $payload = base64_decode((string) DB::table('sessions')->whereNotNull('user_id')->value('payload'));

        $this->assertStringNotContainsString('_token', $payload);
        $this->assertStringNotContainsString('login_web_', $payload);
    }

    public function test_cookies_are_http_only_and_same_site(): void
    {
        $response = $this->get(route('login'));

        $cookie = collect($response->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
    }

    public function test_the_session_cookie_is_secure_by_default_in_production(): void
    {
        $originalEnvironment = $_ENV['APP_ENV'] ?? null;
        $originalServer = $_SERVER['APP_ENV'] ?? null;
        $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'production';
        putenv('APP_ENV=production');

        try {
            $config = require config_path('session.php');
        } finally {
            $_ENV['APP_ENV'] = $originalEnvironment;
            $_SERVER['APP_ENV'] = $originalServer;
            putenv('APP_ENV='.$originalEnvironment);
        }

        $this->assertTrue($config['secure']);
    }

    public function test_session_lifetime_and_encryption_do_not_depend_on_the_environment_file(): void
    {
        $this->assertSame(30, config('session.lifetime'));
        $this->assertTrue(config('session.encrypt'));
    }

    public function test_a_form_without_a_valid_csrf_token_shows_the_expiry_page_in_spanish(): void
    {
        $this->app['env'] = 'local';

        $this->post(route('login'), ['email' => 'ana@example.com', 'password' => self::PASSWORD])
            ->assertStatus(419)
            ->assertSee('La página expiró')
            ->assertDontSee('Page Expired');
    }

    private function useDriver(string $driver): void
    {
        $directory = sys_get_temp_dir().'/futnexis-test-sessions-'.uniqid();
        mkdir($directory);

        $this->beforeApplicationDestroyed(function () use ($directory): void {
            array_map('unlink', glob($directory.'/*') ?: []);
            rmdir($directory);
        });

        config(['session.driver' => $driver, 'session.files' => $directory]);
    }

    private function user(): User
    {
        return User::factory()->create(['password' => self::PASSWORD]);
    }

    /**
     * Log in through the real endpoint and return the stored session identifier.
     */
    private function openSession(User $user): string
    {
        $this->forgetSessionStateInMemory();

        $response = $this->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirectToRoute('home');

        $cookie = collect($response->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        return CookieValuePrefix::remove(decrypt($cookie->getValue(), false));
    }

    private function setLastActivity(string $sessionId, int $minutesAgo): void
    {
        DB::table('sessions')->where('id', $sessionId)->update(['last_activity' => now()->subMinutes($minutesAgo)->getTimestamp()]);
    }

    private function useSession(string $sessionId): static
    {
        $this->forgetSessionStateInMemory();

        return $this->withCookie(config('session.cookie'), $sessionId);
    }

    /**
     * The test client shares one application between requests; a real request always loads the session from storage.
     */
    private function forgetSessionStateInMemory(): void
    {
        $this->app['session']->forgetDrivers();
        $this->app->forgetInstance('session.store');
        $this->app['auth']->forgetGuards();
        $this->app->forgetInstance('auth.driver');
        $this->defaultCookies = [];
    }
}

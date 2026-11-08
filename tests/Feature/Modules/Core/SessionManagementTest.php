<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    private function user(): User
    {
        return User::factory()->create(['password' => self::PASSWORD]);
    }

    /**
     * Log in through the real endpoint and return the stored session identifier.
     */
    private function openSession(User $user): string
    {
        $knownSessions = DB::table('sessions')->pluck('id')->all();

        $this->forgetSessionStateInMemory();
        $this->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirectToRoute('home');

        return DB::table('sessions')->whereNotIn('id', $knownSessions)->value('id');
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

<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\CoreServiceProvider;
use App\Modules\Core\Models\LoginAttempt;
use App\Modules\Core\Support\IpAddress;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        TrustProxies::flushState();
        Request::setTrustedProxies([], -1);

        parent::tearDown();
    }

    public function test_forwarded_headers_are_ignored_by_default(): void
    {
        $this->loginFrom('203.0.113.50');

        $this->assertSame('127.0.0.1', LoginAttempt::query()->sole()->ip_address);
    }

    public function test_forwarded_headers_are_honoured_from_a_configured_proxy(): void
    {
        TrustProxies::at(['127.0.0.1']);

        $this->loginFrom('203.0.113.50');

        $this->assertSame('203.0.113.50', LoginAttempt::query()->sole()->ip_address);
    }

    public function test_forwarded_headers_are_ignored_from_any_other_address(): void
    {
        TrustProxies::at(['10.0.0.1']);

        $this->loginFrom('203.0.113.50');

        $this->assertSame('127.0.0.1', LoginAttempt::query()->sole()->ip_address);
    }

    #[TestWith(['', []])]
    #[TestWith(['*', []])]
    #[TestWith(['**', []])]
    #[TestWith(['10.0.0.1, 10.0.0.0/8', ['10.0.0.1', '10.0.0.0/8']])]
    #[TestWith(['*, 10.0.0.1', ['10.0.0.1']])]
    public function test_the_configuration_never_trusts_every_client(string $configured, array $expected): void
    {
        $this->assertSame($expected, CoreServiceProvider::parseTrustedProxies($configured));
    }

    #[TestWith(['203.0.113.9', '203.0.113.9'])]
    #[TestWith(['::ffff:203.0.113.9', '203.0.113.9'])]
    #[TestWith(['2001:db8:1:2:aaaa::1', '20010db800010002/64'])]
    #[TestWith(['2001:db8:1:2:bbbb::9', '20010db800010002/64'])]
    #[TestWith(['2001:db8:1:3::1', '20010db800010003/64'])]
    #[TestWith(['no-es-una-ip', 'unknown'])]
    public function test_addresses_are_grouped_for_rate_limiting(string $address, string $expected): void
    {
        $this->assertSame($expected, IpAddress::bucket($address));
    }

    private function loginFrom(string $forwardedFor): void
    {
        $user = User::factory()->create(['password' => 'Segura#2026']);

        $this->withHeader('X-Forwarded-For', $forwardedFor)
            ->post(route('login'), ['email' => $user->email, 'password' => 'Segura#2026'])
            ->assertRedirectToRoute('home');
    }
}

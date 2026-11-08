<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Tests\TestCase;
use Throwable;

class MailDeliveryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_connection_failure_gives_a_neutral_answer_and_an_operational_log(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);
        Log::spy();

        $response = $this->post(route('register'), ['email' => 'ana@example.com']);

        $this->assertNeutralStatus($response->getSession()->get('status'));
        $this->assertDeliveryFailureLogged('registration');
    }

    public function test_a_failure_while_sending_gives_a_neutral_answer_and_an_operational_log(): void
    {
        $this->useFailingTransport(new TransportException('Expected response code "250" but got code "451" for ana@example.com'));
        Log::spy();

        $response = $this->post(route('register'), ['email' => 'ana@example.com']);

        $this->assertNeutralStatus($response->getSession()->get('status'));
        $this->assertDeliveryFailureLogged('registration');
        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_the_operational_log_contains_neither_the_token_nor_the_address(): void
    {
        $this->useFailingTransport(new TransportException('Rejected ana@example.com'));
        Log::spy();

        $this->post(route('register'), ['email' => 'ana@example.com']);

        Log::shouldHaveReceived('error')->withArgs(function (string $message, array $context): bool {
            $serialized = (string) json_encode($context);

            return ! str_contains($serialized, 'ana@example.com')
                && preg_match('/[A-Za-z0-9]{64}/', $serialized) === 0
                && array_keys($context) === ['purpose', 'user_id', 'exception'];
        })->once();
    }

    public function test_recovery_gives_the_same_answer_when_the_mail_fails(): void
    {
        $user = User::factory()->create();
        $expected = $this->post(route('password.email'), ['email' => 'nadie@example.com'])->getSession()->get('status');
        $this->useFailingTransport(new TransportException('Connection lost'));
        Log::spy();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $this->assertSame($expected, $response->getSession()->get('status'));
        $this->assertDeliveryFailureLogged('password_reset');
    }

    public function test_a_password_change_is_kept_when_the_notice_cannot_be_sent(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $this->useFailingTransport(new TransportException('Connection lost'));
        Log::spy();

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'Nueva#Clave2026',
            'password_confirmation' => 'Nueva#Clave2026',
        ])->assertRedirectToRoute('login');

        $this->assertDeliveryFailureLogged('password_changed');
    }

    #[TestWith(['josé@dominio.com', true])]
    #[TestWith(['jose@dominio.com', false])]
    public function test_an_international_local_part_needs_smtputf8(string $address, bool $needsSmtpUtf8): void
    {
        $envelope = new Envelope(new Address('remitente@example.com'), [new Address($address)]);

        $this->assertSame($needsSmtpUtf8, $envelope->anyAddressHasUnicodeLocalpart());
    }

    public function test_a_server_without_smtputf8_is_a_logged_failure_with_a_neutral_answer(): void
    {
        $this->useFailingTransport(new InvalidArgumentException('Invalid addresses: non-ASCII characters not supported in local-part of email.'));
        Log::spy();

        $response = $this->post(route('register'), ['email' => 'josé@dominio.com']);

        $this->assertNeutralStatus($response->getSession()->get('status'));
        $this->assertDeliveryFailureLogged('registration');
        $this->assertDatabaseHas('users', ['email' => 'josé@dominio.com']);
    }

    public function test_an_international_address_is_delivered_when_the_transport_accepts_it(): void
    {
        config(['mail.default' => 'array']);
        $user = User::factory()->unverified()->create(['email' => 'josé@dominio.com', 'password' => null]);

        $user->notify(new VerifyEmailNotification(str_repeat('a', 64)));

        $sent = Mail::mailer('array')->getSymfonyTransport()->messages()->first();

        $this->assertSame('josé@dominio.com', $sent->getEnvelope()->getRecipients()[0]->getAddress());
    }

    public function test_an_unexpected_failure_is_contained_as_well(): void
    {
        $user = User::factory()->unverified()->create(['password' => null]);
        $this->useFailingTransport(new RuntimeException('Unexpected'));
        Log::spy();

        $this->post(route('verification.send'), ['email' => $user->email])->assertRedirectToRoute('verification.notice');

        $this->assertDeliveryFailureLogged('verification_resend');
    }

    private function useFailingTransport(Throwable $exception): void
    {
        Mail::extend('failing', fn () => new class($exception) extends AbstractTransport
        {
            public function __construct(private Throwable $exception)
            {
                parent::__construct();
            }

            protected function doSend(SentMessage $message): void
            {
                throw $this->exception;
            }

            public function __toString(): string
            {
                return 'failing://';
            }
        });

        config(['mail.mailers.failing' => ['transport' => 'failing'], 'mail.default' => 'failing']);
    }

    private function assertNeutralStatus(?string $status): void
    {
        $this->assertNotNull($status);
        $this->assertStringNotContainsString('enviamos', mb_strtolower($status));
        $this->assertStringNotContainsString('enviado', mb_strtolower($status));
    }

    private function assertDeliveryFailureLogged(string $purpose): void
    {
        Log::shouldHaveReceived('error')
            ->withArgs(fn (string $message, array $context): bool => $message === 'core.mail.delivery_failed' && $context['purpose'] === $purpose)
            ->once();
    }
}

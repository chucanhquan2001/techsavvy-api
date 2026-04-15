<?php

namespace Tests\Feature;

use App\Mail\ContactCreatedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_contact_successfully(): void
    {
        Mail::fake();
        config([
            'services.contact_notification.recipient_email' => 'owner@example.com',
            'services.contact_notification.recipient_name' => 'Owner',
        ]);

        $user = User::factory()->create();

        $response = $this->postJson('/api/contact', [
            'user_id' => $user->id,
            'content' => 'I need support with my account.',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'ok',
                'message' => 'Contact created successfully',
                'data' => [
                    'user_id' => $user->id,
                    'content' => 'I need support with my account.',
                ],
            ]);

        $this->assertDatabaseHas('contacts', [
            'user_id' => $user->id,
            'content' => 'I need support with my account.',
        ]);

        Mail::assertSent(ContactCreatedMail::class, function (ContactCreatedMail $mail) {
            return $mail->hasTo('owner@example.com')
                && $mail->contactId > 0
                && $mail->contentText === 'I need support with my account.';
        });
    }

    public function test_it_returns_validation_errors_when_payload_is_invalid(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', [
            'content' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Validation failed',
                'data' => null,
            ])
            ->assertJsonStructure([
                'errors' => [
                    'user_id',
                    'content',
                ],
            ]);

        Mail::assertNothingSent();
    }
}

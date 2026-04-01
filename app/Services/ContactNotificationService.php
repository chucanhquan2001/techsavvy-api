<?php

namespace App\Services;

use App\Application\Contact\DTOs\ContactDTO;
use App\Mail\ContactCreatedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactNotificationService
{
    public function sendNewContactNotification(ContactDTO $contact): void
    {
        $recipientEmail = config('services.contact_notification.recipient_email');
        $recipientName = config('services.contact_notification.recipient_name');

        if (blank($recipientEmail)) {
            Log::warning('Contact notification email is not configured.', [
                'contact_id' => $contact->id,
            ]);

            return;
        }

        try {
            Mail::to($recipientEmail, $recipientName)->send(
                new ContactCreatedMail(
                    contactId: $contact->id,
                    contentText: $contact->content
                )
            );
        } catch (Throwable $e) {
            Log::error('Failed to send contact notification email.', [
                'contact_id' => $contact->id,
                'user_id' => $contact->userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

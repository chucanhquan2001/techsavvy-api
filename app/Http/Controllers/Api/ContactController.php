<?php

namespace App\Http\Controllers\Api;

use App\Application\Contact\Commands\CreateContactCommand;
use App\Application\Contact\Usecases\CreateContactUseCase;
use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Services\ContactNotificationService;
use Throwable;

class ContactController extends Controller
{
    /**
     * Store a new contact message.
     */
    public function store(
        StoreContactRequest $request,
        CreateContactUseCase $useCase,
        ContactNotificationService $notificationService
    )
    {
        try {
            $command = new CreateContactCommand(
                userId: (int) $request->input('user_id'),
                content: $request->input('content')
            );

            $contact = $useCase->execute($command);
            $notificationService->sendNewContactNotification($contact);

            return ApiResponse::ok(
                $contact->toArray(),
                'Contact created successfully',
                HttpStatus::CREATED
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to create contact', $e);
        }
    }
}

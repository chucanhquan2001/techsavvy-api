<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Contact;

interface ContactRepositoryInterface
{
    public function create(Contact $contact): Contact;
}

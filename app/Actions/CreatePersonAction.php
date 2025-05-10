<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

final class CreatePersonAction
{
    /**
     * Execute the action.
     */
    public function handle(array $data): Person
    {
        return DB::transaction(function () use ($data): Person {
            // Create the person with the general information
            $generalData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'],
                'civil_status' => $data['civil_status'] ?? null,
                'dob' => $data['dob'] ?? null,
            ];
            $person = Person::create($generalData);

            // Fill spiritual information
            $spiritualData = [
                'membership_at' => $data['membership_at'] ?? null,
                'baptized_at' => $data['baptized_at'] ?? null,
                'saved_at' => $data['saved_at'] ?? null,
                'testimony' => $data['testimony'] ?? null,
            ];
            $person->spiritualInformation()->create($spiritualData);

            // Fill contact information
            $contactData = [
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'alternate_phone' => $data['alternate_phone'] ?? null,
                'address_line_1' => $data['address_line_1'] ?? null,
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
            ];
            $person->contactInformation()->create($contactData);

            // Refresh the person instance to get the latest data
            $person->refresh();

            return $person;
        });
    }
}

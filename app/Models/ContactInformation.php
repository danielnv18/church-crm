<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ContactInformation extends Model
{
    protected $fillable = [
        'person_id',
        'email',
        'phone',
        'alternate_phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}

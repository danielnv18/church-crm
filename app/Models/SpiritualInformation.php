<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SpiritualInformation extends Model
{
    /** @use HasFactory<\Database\Factories\SpiritualInformationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'membership_at' => 'date',
            'baptized_at' => 'date',
            'saved_at' => 'date',
        ];
    }
}

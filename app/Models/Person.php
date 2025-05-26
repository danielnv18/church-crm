<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CivilStatus;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'civil_status',
        'dob',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'gender' => Gender::class,
        'civil_status' => CivilStatus::class,
    ];

    /**
     * @return HasOne<ContactInformation, Person>
     */
    public function contactInformation(): HasOne
    {
        return $this->hasOne(ContactInformation::class);
    }

    /**
     * @return HasOne<SpiritualInformation, Person>
     */
    public function spiritualInformation(): HasOne
    {
        return $this->hasOne(SpiritualInformation::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

final class DeletePersonAction
{
    /**
     * Execute the action.
     */
    public function handle(Person $person): void
    {
        DB::transaction(function () use ($person): void {
            $person->delete();
        });
    }
}

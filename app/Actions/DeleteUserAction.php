<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteUserAction
{
    /**
     * Execute the action.
     */
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->delete();
        });
    }
}

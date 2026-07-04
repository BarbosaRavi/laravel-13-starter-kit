<?php

namespace App\Models;

use App\Enums\UserTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['user_id'])]
class Admin extends Model
{
    use HasUuids, LogsActivity;


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

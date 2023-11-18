<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class APIRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'ip_address',
        'method',
        'user_id',
        'request_description',
        'request',
        'parameters',
        'response',
        'status',
        'user_agent'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

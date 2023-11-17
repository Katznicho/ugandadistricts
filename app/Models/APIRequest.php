<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

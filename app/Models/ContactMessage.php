<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
        'business_id',
    ];

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }
}

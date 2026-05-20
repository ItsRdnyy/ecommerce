<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_address',
        'business_phone',
        'logo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'business_id', 'user_id');
    }

    public function shippingRules()
    {
        return $this->hasMany(ShippingRule::class, 'business_id', 'user_id');
    }

    public function getStoreSlug(): string
    {
        return strtolower(str_replace(' ', '-', $this->business_name));
    }
}

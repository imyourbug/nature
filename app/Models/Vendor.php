<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'status',
        'verified',
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];

    /**
     * Get the products for the vendor.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the RFQs for the vendor.
     */
    public function rfqs()
    {
        return $this->hasMany(RFQ::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'price',
        'specs',
        'status',
        'vendor_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'specs' => 'array',
    ];

    /**
     * Get the vendor that owns the product.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the RFQs for the product.
     */
    public function rfqs()
    {
        return $this->hasMany(RFQ::class);
    }
}

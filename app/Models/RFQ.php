<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFQ extends Model
{
    use HasFactory;

    protected $table = 'rfqs';

    protected $fillable = [
        'product_id',
        'vendor_id',
        'quantity',
        'status',
    ];

    /**
     * Get the product that the RFQ is for.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the vendor that the RFQ is sent to.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

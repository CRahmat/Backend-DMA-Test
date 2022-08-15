<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'date',
        'customer_name',
        'salesperson_name',
        'payment_type',
        'notes',
        'products',
        'list_products_sold'
    ];

    protected $casts = [
        'list_products_sold' => 'array'
    ];
}

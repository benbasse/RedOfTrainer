<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis_line_items extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'title',
        'date',
        'description',
        'unit_price_ht',
        'vat',
        'unit_price_ttc',
        'discount',
        'line_total_ht',
        'devis_id'
    ];

    public function Devis()
    {
        return $this->belongsToMany(Devis::class);
    }
}

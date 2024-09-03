<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Line_items extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'date', 'description', 'unit_price_ht', 'vat', 'unit_price_ttc', 'discount', 'line_total_ht', 'facture_id'
    ];

    public function Facture(){
        return $this->belongsToMany(Facture::class, 'facture_id');
    }
}

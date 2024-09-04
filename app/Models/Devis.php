<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'devis_number',
        'client_id',
        'due_date',
        'user_id',
        'total_amount_ht',
        'total_vat',
        'total_amount_ttc',
        'special_conditions',
        'internal_notes',
        'sent_to',
    ];

    public function Devis_Line_items(){
        return $this->hasMany(Devis_line_items::class, 'devis_id');
    }

    public function User(){
        return $this->belongsToMany(User::class, 'user_id');
    }

    public function Client(){
        return $this->belongsToMany(Client::class, 'client_id');
    }
}

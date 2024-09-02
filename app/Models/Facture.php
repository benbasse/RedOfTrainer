<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'facture_number',
        'client_id',
        'due_date',
        'user_id',
        'total_amount_ht',
        'total_vat',
        'total_amount_ttc',
        'status',
        'payment_date',
        'payment_method',
        'internal_notes',
        'sent_to',
        'auto_reminder',
    ];

    public function User(){
        return $this->belongsToMany(User::class, 'user_id');
    }

    public function line_items(){
        return $this->hasMany(Line_items::class, 'facture_id');
    }

    public function Client(){
        return $this->belongsToMany(Client::class, 'client_id');
    }

}

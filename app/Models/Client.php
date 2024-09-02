<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'address',
        'siret_siren',
        'type',
        'user_id'
    ];

    public function user(){
        return $this->belongsToMany(User::class, 'user_id');
    }

    public function facture(){
        return $this->hasMany(Facture::class, 'client_id');
    }
}

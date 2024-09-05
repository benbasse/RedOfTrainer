<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bilan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year',
        'total_invoices_ht',
        'total_session_hours',
        'total_session',
        'total_trainees',
        'user_id'
    ];

    public function User(){
        return $this->belongsToMany(User::class);
    }
}

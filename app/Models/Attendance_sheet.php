<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance_sheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'path',
    ];

    public function Session(){
        return $this->belongsTo(Session::class);
    }
}

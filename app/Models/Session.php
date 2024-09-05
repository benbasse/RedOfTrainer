<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'date',
        'address',
        'duration',
        'number_of_trainees',
        'human_verification',
        'trainer_verification_notes',
        'total_hours',
        // 'session_status',
        'attendance_sheet_id',
        'manual_validation',
        'user_id'
    ];

    public function User(){
        return $this->belongsToMany(User::class, 'user_id');
    }

    public function Attendance_sheet(){
        return $this->belongsTo(Attendance_sheet::class, 'attendance_sheet_id');
    }
}

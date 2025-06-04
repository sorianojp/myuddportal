<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'LOGIN_INFO'; // Authentication info is stored here
    protected $primaryKey = 'LOGIN_INDEX'; // Primary key for the login table
    public $timestamps = false;

    protected $hidden = [
        'PASSWORD'
    ];

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'USER_INDEX', 'USER_INDEX');
    }

    public function getCourseAttribute()
    {
        return DB::table('STUD_CURRICULUM_HIST as sch')
            ->join('COURSE_OFFERED as co', 'co.COURSE_INDEX', '=', 'sch.COURSE_INDEX')
            ->where('sch.USER_INDEX', $this->USER_INDEX)
            ->orderByDesc('sch.DATE_ENROLLED')
            ->select('co.COURSE_CODE', 'co.COURSE_NAME')
            ->first();
    }

}

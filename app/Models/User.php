<?php

namespace App\Models;
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

    public function finalGrades()
    {
        return $this->hasMany(Grade::class, 'user_index_', 'USER_INDEX');
    }

    public function termGrades()
    {
        return $this->hasMany(TermGrade::class, 'user_index_', 'USER_INDEX');
    }

    public function curriculumHistories()
    {
        return $this->hasMany(CurriculumHistory::class, 'USER_INDEX', 'USER_INDEX');
    }

    public function getCourseAttribute(): ?array
    {
        $history = $this->curriculumHistories()
                        ->with('courseOffered')
                        ->orderByDesc('DATE_ENROLLED')
                        ->first();

        return optional($history->courseOffered)
                   // only pull back the two columns you need
                   ->only(['COURSE_NAME', 'COURSE_CODE']);
    }


}

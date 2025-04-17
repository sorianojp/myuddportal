<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $table = 'STUDENT_ENROLLMENT'; // or your actual table name
    protected $primaryKey = 'SE_INDEX'; // or primary key if different
    public $timestamps = false;

    public function subjectSection()
    {
        return $this->belongsTo(SubjectSection::class, 'SUB_SEC_INDEX', 'SUB_SEC_INDEX');
    }
}

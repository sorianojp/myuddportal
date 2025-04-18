<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOffered extends Model
{
    protected $table = 'COURSE_OFFERED';
    protected $primaryKey = 'COURSE_INDEX';
    public $timestamps = false;

    public function curriculumHistories()
    {
        return $this->hasMany(CurriculumHistory::class, 'COURSE_INDEX', 'COURSE_INDEX');
    }
}

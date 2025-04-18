<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CurriculumHistory extends Model
{
    protected $table = 'STUD_CURRICULUM_HIST';
    protected $primaryKey = 'CUR_HIST_INDEX';
    public $timestamps = false;
}

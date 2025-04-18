<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'G_SHEET_FINAL';
    protected $primaryKey = 'GS_INDEX';
    public $timestamps = false;

    public function subSection()
    {
        return $this->belongsTo(SubSection::class, 'SUB_SEC_INDEX', 'SUB_SEC_INDEX');
    }
    public function remark()
    {
        return $this->belongsTo(Remark::class, 'REMARK_INDEX', 'REMARK_INDEX');
    }
    public function encodedByUser()
    {
        return $this->belongsTo(UserProfile::class, 'ENCODED_BY', 'USER_INDEX');
    }
    public function curriculum()
    {
        return $this->belongsTo(CurriculumHistory::class, 'CUR_HIST_INDEX', 'CUR_HIST_INDEX');
    }

    public function scopeValid($query)
    {
        return $query->where('IS_VALID', 1)->where('IS_DEL', 0);
    }

}

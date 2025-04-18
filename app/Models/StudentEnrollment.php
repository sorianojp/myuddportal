<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $table      = 'ENRL_FINAL_CUR_LIST';   // your enrollment view/table
    protected $primaryKey = 'ENROLL_INDEX';            // adjust if different
    public    $timestamps = false;

    // only “valid” & “not deleted”
    public function scopeValid($q)
    {
        return $q->where('IS_VALID', 1)->where('IS_DEL', 0);
    }

    // link to the section
    public function subSection()
    {
        return $this->belongsTo(SubSection::class, 'SUB_SEC_INDEX', 'SUB_SEC_INDEX');
    }
}

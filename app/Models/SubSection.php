<?php

namespace App\Models;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    protected $table = 'E_SUB_SECTION';
    protected $primaryKey = 'SUB_SEC_INDEX';
    public $timestamps = false;

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SUB_INDEX', 'SUB_INDEX');
    }
    public function roomAssigns()
    {
        return $this->hasMany(RoomAssign::class, 'SUB_SEC_INDEX', 'SUB_SEC_INDEX');
    }
    
    public function scopeValid($q)
    {
        return $q->where('IS_VALID', 1)->where('IS_DEL', 0);
    }

}

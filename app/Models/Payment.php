<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'FA_STUD_PAYMENT';
    protected $primaryKey = 'PAYMENT_INDEX';
    public $timestamps = false;

    public function otherSchoolFee()
    {
        return $this->belongsTo(OtherSchoolFee::class, 'OTHSCH_FEE_INDEX', 'OTHSCH_FEE_INDEX');
    }

}

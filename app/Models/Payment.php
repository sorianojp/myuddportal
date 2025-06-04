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
    public function createdBy() {
        return $this->belongsTo(User::class, 'CREATED_BY', 'USER_INDEX');
    }

    public function cashAdvanceFrom() {
        return $this->belongsTo(User::class, 'CASH_ADV_FROM_EMP_ID', 'USER_INDEX');
    }

    public function refund() {
        return $this->hasOne(StudentRefund::class, 'REFUND_TO_PMT_INDEX', 'PAYMENT_INDEX');
    }

    public function journalVoucher() {
        return $this->belongsTo(JournalVoucher::class, 'JV_INDEX', 'JV_INDEX');
    }
}

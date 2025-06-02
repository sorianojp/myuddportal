<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRefund extends Model
{
    protected $table = 'FA_STUD_REFUND';
    protected $primaryKey = 'REFUND_INDEX';
    public $timestamps = false;
}

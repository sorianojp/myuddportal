<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherSchoolFee extends Model
{
    protected $table = 'FA_OTH_SCH_FEE';
    protected $primaryKey = 'OTHSCH_FEE_INDEX';
    public $timestamps = false;
}

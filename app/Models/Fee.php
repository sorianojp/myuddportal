<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'FA_FEE_HISTORY';
    protected $primaryKey = 'fee_hist_index';
    public $timestamps = false;
}

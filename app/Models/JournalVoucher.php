<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    protected $table = 'AC_JV';
    protected $primaryKey = 'JV_INDEX';
    public $timestamps = false;
}

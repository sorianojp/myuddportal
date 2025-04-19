<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentSchoolYear extends Model
{
    protected $table = 'CURRENT_SCHOOLYR';

    protected $primaryKey = 'CUR_SCHYR_INDEX';

    public $timestamps = false;
}

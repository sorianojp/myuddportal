<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentSchoolYear extends Model
{
    protected $table = 'CURRENT_SCHOOLYR';

    protected $primaryKey = 'CUR_SCHYR_INDEX';

    public $timestamps = false;


    public static function getCurrent(): self
    {
        return self::where('IS_OPEN', 1)
            ->where('IS_DEL', 0)
            ->latest('CUR_SCHYR_INDEX')
            ->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomDetail extends Model
{
    protected $table      = 'E_ROOM_DETAIL';
    protected $primaryKey = 'ROOM_INDEX';
    public    $timestamps = false;
}

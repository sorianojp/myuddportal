<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssign extends Model
{
    protected $table = 'E_ROOM_ASSIGN';
    protected $primaryKey = 'ASSIGN_INDEX';   // adjust if different
    public    $timestamps = false;

    public function roomDetail()
    {
        return $this->belongsTo(RoomDetail::class, 'ROOM_INDEX', 'ROOM_INDEX');
    }

}

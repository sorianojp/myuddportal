<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyLoad extends Model
{
    protected $table      = 'FACULTY_LOAD';
    protected $primaryKey = 'LOAD_INDEX';    // adjust if needed
    public    $timestamps = false;

    public function faculty()
    {
        return $this->belongsTo(UserProfile::class, 'USER_INDEX', 'USER_INDEX');
    }
}

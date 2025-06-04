<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'USER_TABLE';
    protected $primaryKey = 'USER_INDEX';
    public $timestamps = false;

    public function login()
    {
        return $this->hasOne(User::class, 'USER_INDEX', 'USER_INDEX');
    }

    public function getFullNameAttribute()
    {
        return "{$this->LNAME}, {$this->FNAME} {$this->MNAME}";
    }

}

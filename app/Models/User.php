<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    public function contact()
    {
        return $this->hasMany('App\Models\Contact', 'user_id');
    }
}

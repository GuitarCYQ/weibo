<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //一对多
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

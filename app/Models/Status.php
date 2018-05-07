<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];

    // 微博属于某一个用户的
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

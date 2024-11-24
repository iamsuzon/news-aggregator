<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

//    public function articles()
//    {
//        return $this->hasMany(Article::class);
//    }
}

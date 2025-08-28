<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['category_name'];

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

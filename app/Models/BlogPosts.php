<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPosts extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description','created_by','updated_by',
    ];

    public function user()
    {
    	 return $this->hasOne(
            User::class,
            'id',
            'created_by'
        );
    }

    public function comments()
    {
         return $this->hasMany(
            BlogComments::class,
            'blog_id'
        );
    }
}

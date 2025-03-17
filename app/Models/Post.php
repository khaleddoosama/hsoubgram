<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'description',
        'slug',
        'likes', 
        'image',
        'user_id',
    ];

    public function owner()
    {
        return  $this->belongsTo(User::class,'user_id');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function likes()
    {
        return $this->belongsToMany(User::class,'likes');
    }

    public function liked($userid)
    {
        return $this->likes()->where('user_id',$userid)->exists();
    }

}
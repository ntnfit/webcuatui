<?php

namespace App\Traits;

//use Firefly\FilamentBlog\Models\Comment;
use App\Models\blogs as Post;

trait HasBlog
{

    public function name()
    {
        return $this->{'name'};
    }

    public function getAvatarAttribute()
    {
        return $this->{'profile_photo_path'}
            ? asset('storage/'.$this->{'profile_photo_path'}) : 'https://ui-avatars.com/api/?&background=random&name='.$this->{'name'};
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'user_id');
    // }
}

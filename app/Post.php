<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use SoftDeletes;

    protected $dates = [
        'published_at'
    ];
    
    protected $fillable = [
        'title',
        'description',
        'content',
        'image',
        'user_id',
        'published_at',
        'category_id'
    ];

    //delete post image from storage
    public function deleteImage(){
        Storage::delete($this->image);
    }

    //defining relationship
    //category is name of model Post belongs to
    public function category(){
        return $this->belongsTo(Category::class);
    }

    //tags plural because it can have more than one tag
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    //checks if post has tag
    public function hasTag($tagId){
        return in_array($tagId, $this->tags->pluck('id')->toArray());
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query){
        return $query->where('published_at', '<=', now());
    }

    //makes query simplier
    public function scopeSearched($query){
        $search = request()->query('search');

        if(!$search){
            return $query->published();
        }

        return $query->published()->where('title', 'LIKE', "%{$search}%");

    }
}

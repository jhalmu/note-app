<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Note extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'user_id',
        'updater_id',
        'is_published',
        'type',
        'content',
        'body',
        'title',
        'slug',
        'plaintext',
        'image',
    ];

    // belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // belongs to many tags/ can have many tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /// USER ID BELONGS????
}

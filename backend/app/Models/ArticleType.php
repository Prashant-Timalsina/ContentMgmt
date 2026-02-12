<?php

namespace App\Models;

use App\Models\Content;
use Illuminate\Database\Eloquent\Model;

class ArticleType extends Model
{
    //
    protected $fillable = ['name','slug','description'];

    public function articles()
    {
        return $this->hasMany(Content::class);
    }
}

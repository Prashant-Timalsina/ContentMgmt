<?php

namespace App\Models;

use App\Models\ArticleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Content extends Model
{
    //
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'body',
        'slug',
        'type_id',
        'author_id',
        'status',
        'submitted_at',
        'published_at',
        'rejection_reason'
    ];

        protected $casts = [
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    // Relationships

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function type()
    {
        return $this->belongsTo(ArticleType::class);
    }

    //Helper to get the statuses
    public static function getStatuses() : array 
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED ,
            self::STATUS_REJECTED ,
            self::STATUS_PUBLISHED ,
        ];
    }

}

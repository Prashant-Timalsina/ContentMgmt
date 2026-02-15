<?php

namespace Database\Factories;

use App\Models\ArticleType;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        $title = fake()->sentence();


        return [
            'title' => $title,
            'body' => fake()->paragraphs(3, true),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'status' => Content::STATUS_DRAFT,
            'type_id' => ArticleType::inRandomOrder()->value('id') ?? 1,
        ];

    }
}

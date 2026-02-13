<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ArticleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $types = [
            'Political',
            'Economic',
            'Sports',
            'Technology',
            'Entertainment'
        ];

        foreach ($types as $type){
            ArticleType::create([
                'name' => $type,
                'slug' => Str::slug($type)
            ]);
        }
    }
}

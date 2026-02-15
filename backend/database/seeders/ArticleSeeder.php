<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run($author_id = null, $count = 10, $type_id = null)
    {
        if (!$author_id) {
            throw new Exception("Author is required");
        }

        $user = User::findOrFail($author_id);

        $factory = Content::factory()->count($count);

        // Override type only if provided
        if ($type_id !== null) {
            $factory = $factory->state([
                'type_id' => $type_id
            ]);
        }

        $factory->create([
            'author_id' => $user->id
        ]);
    }
}

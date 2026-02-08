<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures roles and tokens use App\Models\User so auth and Spatie permissions match.
     */
    public function up(): void
    {
        $userClass = 'App\Models\User';
        $oldClass = 'App\Http\Models\User';

        if (config('permission.table_names.model_has_roles')) {
            DB::table(config('permission.table_names.model_has_roles'))
                ->where('model_type', $oldClass)
                ->update(['model_type' => $userClass]);
        }

        DB::table('personal_access_tokens')
            ->where('tokenable_type', $oldClass)
            ->update(['tokenable_type' => $userClass]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $userClass = 'App\Models\User';
        $oldClass = 'App\Http\Models\User';

        if (config('permission.table_names.model_has_roles')) {
            DB::table(config('permission.table_names.model_has_roles'))
                ->where('model_type', $userClass)
                ->update(['model_type' => $oldClass]);
        }

        DB::table('personal_access_tokens')
            ->where('tokenable_type', $userClass)
            ->update(['tokenable_type' => $oldClass]);
    }
};

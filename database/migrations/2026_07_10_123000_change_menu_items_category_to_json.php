<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing category strings to JSON arrays
        DB::table('menu_items')->get()->each(function ($item) {
            $category = strtolower(trim($item->category ?? 'other'));
            $categories = in_array($category, ['breakfast', 'lunch', 'dinner', 'beverages']) ? [$category] : ['other'];
            DB::table('menu_items')->where('id', $item->id)->update([
                'category' => json_encode($categories),
            ]);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->renameColumn('category', 'categories');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->json('categories')->change();
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('categories')->change();
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->renameColumn('categories', 'category');
        });

        DB::table('menu_items')->get()->each(function ($item) {
            $categories = json_decode($item->category ?? '["other"]', true);
            DB::table('menu_items')->where('id', $item->id)->update([
                'category' => $categories[0] ?? 'other',
            ]);
        });
    }
};

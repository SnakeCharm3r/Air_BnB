<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            'cctv'       => 'cctv',
            'ac'         => 'ac',
            'water_pump' => 'water-pump',
            'generator'  => 'generator',
            'solar'      => 'solar',
            'other'      => 'other',
        ];

        foreach ($mapping as $deviceType => $slug) {
            $category = DB::table('infrastructure_categories')->where('slug', $slug)->first();
            if ($category) {
                DB::table('infrastructure_devices')
                    ->where('device_type', $deviceType)
                    ->whereNull('category_id')
                    ->update(['category_id' => $category->id]);
            }
        }
    }

    public function down(): void
    {
        DB::table('infrastructure_devices')->update(['category_id' => null]);
    }
};

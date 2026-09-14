<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $now = now();
        $defaults = [
            ['name' => 'Müzik', 'slug' => 'music'],
            ['name' => 'Tiyatro', 'slug' => 'theater'],
            ['name' => 'Atölye', 'slug' => 'workshop'],
        ];

        foreach ($defaults as $row) {
            DB::table('event_categories')->insert([
                ...$row,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('event_category_id')
                ->nullable()
                ->after('city')
                ->constrained('event_categories')
                ->nullOnDelete();
        });

        $map = DB::table('event_categories')->pluck('id', 'slug');

        foreach ($map as $slug => $id) {
            DB::table('events')->where('category', $slug)->update(['event_category_id' => $id]);
        }

        $fallback = $map['music'] ?? $map->first();
        DB::table('events')->whereNull('event_category_id')->update(['event_category_id' => $fallback]);

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('category')->default('music')->after('city');
        });

        $map = DB::table('event_categories')->pluck('slug', 'id');

        foreach ($map as $id => $slug) {
            DB::table('events')->where('event_category_id', $id)->update(['category' => $slug]);
        }

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_category_id');
        });

        Schema::dropIfExists('event_categories');
    }
};

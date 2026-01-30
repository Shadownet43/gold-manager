<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropPrimary(['key']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->id()->first();
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        DB::table('settings')->whereNull('user_id')->update(['user_id' => 1]);

        Schema::table('settings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'key']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->primary('key');
        });
    }
};

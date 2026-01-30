<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // TiDB tidak support drop primary key pada clustered index
        // Solusi: Buat tabel baru, copy data, hapus tabel lama, rename
        
        // 1. Buat tabel baru dengan struktur yang benar
        Schema::create('settings_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('key');
            $table->integer('value')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'key']);
            $table->index('user_id');
        });
        
        // 2. Copy data dari tabel lama (jika ada)
        $oldData = DB::table('settings')->get();
        foreach ($oldData as $row) {
            DB::table('settings_new')->insert([
                'user_id' => 1, // Default user_id
                'key' => $row->key,
                'value' => $row->value,
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now(),
            ]);
        }
        
        // 3. Hapus tabel lama
        Schema::dropIfExists('settings');
        
        // 4. Rename tabel baru
        Schema::rename('settings_new', 'settings');
    }

    public function down(): void
    {
        // Buat ulang tabel dengan struktur lama
        Schema::create('settings_old', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->integer('value')->default(0);
            $table->timestamps();
        });
        
        // Copy data
        $data = DB::table('settings')->get();
        foreach ($data as $row) {
            DB::table('settings_old')->insertOrIgnore([
                'key' => $row->key,
                'value' => $row->value,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
        
        Schema::dropIfExists('settings');
        Schema::rename('settings_old', 'settings');
    }
};

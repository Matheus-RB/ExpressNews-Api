<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->timestamps();
    });

    DB::table('categories')->insert([
      ['name' => 'Ciência', 'created_at' => now()],
      ['name' => 'Economia', 'created_at' => now()],
      ['name' => 'Esportes', 'created_at' => now()],
      ['name' => 'Internacional', 'created_at' => now()],
      ['name' => 'Política', 'created_at' => now()],
    ]);
  }
  public function down(): void
  {
    Schema::dropIfExists('categories');
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('favorite_news', function (Blueprint $table) {
      $table->id();
      $table->foreignId('news_id')->constrained();
      $table->foreignId('user_id')->constrained();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('favorite_news');
  }
};

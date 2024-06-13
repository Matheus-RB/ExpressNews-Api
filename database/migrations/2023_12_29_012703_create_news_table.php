<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('news', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('slug')->unique()->nullable();
      $table->string('image_description');
      $table->string('introductory_paragraph', 400);
      $table->string('main_image');
      $table->text('content');
      $table->foreignId('category_id')->constrained();
      $table->foreignId('user_id')->constrained();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('news');
  }
};

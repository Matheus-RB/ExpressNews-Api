<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('subcomments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('comment_id')->constrained();
      $table->foreignId('user_id')->constrained();
      $table->text('content');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('subcomments');
  }
};

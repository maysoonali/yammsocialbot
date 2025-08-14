<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assignees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('availability_stat', 50)->nullable();
            $table->string('team', 100)->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('assignees');
    }
};

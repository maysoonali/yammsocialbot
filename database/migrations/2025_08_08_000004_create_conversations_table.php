<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->uuid('contact_id');
            $table->uuid('assignee_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('channel', 50)->nullable();
            $table->jsonb('labels')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('assignees')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('conversations');
    }
};

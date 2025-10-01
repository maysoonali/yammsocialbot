<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullSchema extends Migration
{
    public function up(): void
{
    // Drop tables if they exist (already in your file)
    Schema::dropIfExists('webhook_events');
    Schema::dropIfExists('message_payloads');
    Schema::dropIfExists('messages');
    Schema::dropIfExists('conversation_metrics');
    Schema::dropIfExists('conversations');
    Schema::dropIfExists('users');
    Schema::dropIfExists('assignees');
    Schema::dropIfExists('accounts');
    Schema::dropIfExists('migrations');

    // accounts
    Schema::create('accounts', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->timestamps(); 
});


    // assignees
    Schema::create('assignees', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name', 255);
        $table->string('availability_stat', 50)->nullable();
        $table->string('team', 100)->nullable();
        $table->timestamps();
    });

    // users
    Schema::create('users', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('account_id')->nullable();
        $table->string('name', 255)->nullable();
        $table->boolean('is_business')->nullable();
        $table->string('phone_number', 20)->nullable();
        $table->string('email', 255)->nullable();
        $table->uuid('yamm_customer_id')->nullable();
        $table->timestamps();
        $table->foreign('account_id')->references('id')->on('accounts');
    });

    // conversations
    Schema::create('conversations', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('account_id')->nullable();
        $table->uuid('contact_id')->nullable();
        $table->uuid('assignee_id')->nullable();
        $table->string('status', 50)->nullable();
        $table->string('channel', 50)->nullable();
        $table->jsonb('labels')->nullable();
        $table->timestamps();
        $table->foreign('account_id')->references('id')->on('accounts');
        $table->foreign('assignee_id')->references('id')->on('assignees');
        $table->foreign('contact_id')->references('id')->on('users');
    });

   

    // messages
    Schema::create('messages', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('account_id')->nullable();
        $table->uuid('conversation_id')->nullable();
        $table->uuid('sender_id')->nullable();
        $table->text('sender_type')->nullable();
        $table->text('message_type')->nullable();
        $table->text('content')->nullable();
        $table->text('content_type')->nullable();
        $table->text('status')->nullable();
        $table->boolean('private')->nullable();
        $table->timestamps();
        $table->boolean('payload_exist')->nullable();

        $table->foreign('account_id')->references('id')->on('accounts');
        $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');

        
    });

    // message_payloads
    Schema::create('message_payloads', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('message_id')->nullable();
        $table->string('title', 255)->nullable();
        $table->text('payload')->nullable();
        $table->string('type', 100)->nullable();
        $table->string('image_url', 500)->nullable();
        $table->text('footer')->nullable();
        $table->timestamps();

        $table->foreign('message_id')->references('id')->on('messages');
    });

    // webhook_events
    Schema::create('webhook_events', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('event_type', 100)->nullable();
        $table->jsonb('raw_payload')->nullable();
        $table->timestamp('received_at')->nullable();
        $table->uuid('message_id')->nullable();
        $table->timestamps();

        $table->foreign('message_id')->references('id')->on('messages');
    });

    // migrations (optional if Laravel handles this automatically)
    Schema::create('migrations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('migration', 255);
        $table->integer('batch');
    });
}

    public function down()
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('message_payloads');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('users');
        Schema::dropIfExists('assignees');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('migrations');
    }
}
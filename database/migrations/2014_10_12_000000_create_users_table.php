<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 40)->nullable();
            $table->string('last_name', 40)->nullable();
            $table->string('uuid', 36)->unique();
            $table->string('username', 40)->unique()->comment('Username for login');
            $table->string('email')->unique()->comment('Email address for login');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable()->comment('User avatar url');
            $table->string('phone')->nullable()->comment('User phone');
            $table->timestamp('phone_verified_at')->nullable()->comment('User phone verified at');
            $table->date('birthday')->nullable()->comment('User birthday');
            $table->string('nationality_code', 2)->nullable()->comment('User nationality');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
